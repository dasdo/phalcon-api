<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\UsersInvite;
use Gewaer\Models\Users;
use Gewaer\Models\UsersAssociatedCompany;
use Gewaer\Models\Roles;
use Phalcon\Security\Random;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Exception\ServerErrorHttpException;
use Phalcon\Http\Response;
use Exception;
use Gewaer\Exception\ModelException;
use Gewaer\Traits\AuthTrait;

/**
 * Class LanguagesController
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property Apps $app
 * @property Mail $mail
 * @property Auth $auth
 * @property Payload $payload
 * @property Exp $exp
 * @property JWT $jwt
 * @package Gewaer\Api\Controllers
 *
 */
class UsersInviteController extends BaseController
{
    use AuthTrait;

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['invite_hash', 'companies_id', 'role_id', 'app_id', 'email'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['invite_hash', 'companies_id', 'role_id', 'app_id', 'email'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new UsersInvite();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['companies_id', ':', $this->userData->currentCompanyId()],
        ];
    }

    /**
     * Get users invite by hash
     * @param string $hash
     * @return Response
     */
    public function getByHash(string $hash): Response
    {
        $userInvite = $this->model::findFirst([
            'conditions' => 'invite_hash =  ?0 and is_deleted = 0',
            'bind' => [$hash]
        ]);

        if (!is_object($userInvite)) {
            throw new NotFoundHttpException('Users Invite not found');
        }

        return $this->response($userInvite);
    }

    /**
     * Sets up invitation information for a would be user
     * @return Response
     */
    public function insertInvite(): Response
    {
        $request = $this->request->getPost();
        $random = new Random();

        $validation = new Validation();
        $validation->add('email', new PresenceOf(['message' => _('The email is required.')]));
        $validation->add('role_id', new PresenceOf(['message' => _('The role is required.')]));

        //validate this form for password
        $messages = $validation->validate($this->request->getPost());
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new ServerErrorHttpException((string)$message);
            }
        }

        //Check if user was already was invited to current company and return message
        $invitedUser = $this->model::findFirst([
            'conditions' => 'email = ?0 and companies_id = ?1 and role_id = ?2',
            'bind' => [$request['email'], $this->userData->default_company, $request['role_id']]
        ]);

        if (is_object($invitedUser)) {
            throw new ModelException('User already invited to this company and added with this role');
        }

        //Save data to users_invite table and generate a hash for the invite
        $userInvite = $this->model;
        $userInvite->companies_id = $this->userData->default_company;
        $userInvite->app_id = $this->app->getId();
        $userInvite->role_id = Roles::existsById((int)$request['role_id'])->id;
        $userInvite->email = $request['email'];
        $userInvite->invite_hash = $random->base58();
        $userInvite->created_at = date('Y-m-d H:m:s');

        if (!$userInvite->save()) {
            throw new UnprocessableEntityHttpException((string) current($userInvite->getMessages()));
        }

        $this->sendInviteEmail($request['email'], $userInvite->invite_hash);
        return $this->response($userInvite);
    }

    /**
     * Send users invite email
     * @param string $email
     * @return void
     */
    private function sendInviteEmail(string $email, string $hash): void
    {
        $userExists = Users::findFirst([
            'conditions' => 'email = ?0 and is_deleted = 0',
            'bind' => [$email]
        ]);

        $invitationUrl = $this->config->app->frontEndUrl . '/users/invites/' . $hash;

        if (is_object($userExists)) {
            $invitationUrl = $this->config->app->frontEndUrl . '/users/link/' . $hash;
        }

        if (!defined('API_TESTS')) {
            $subject = _('You have been invited!');
            $this->mail
            ->to($email)
            ->subject($subject)
            ->content($invitationUrl)
            ->sendNow();
        }
    }

    /**
     * Add invited user to our system
     * @return Response
     */
    public function processUserInvite(string $hash): Response
    {
        $request = $this->request->getPost();
        $password = ltrim(trim($request['password']));

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //Ok let validate user password
        $validation = new Validation();
        $validation->add('password', new PresenceOf(['message' => _('The password is required.')]));

        $validation->add(
            'password',
            new StringLength([
                'min' => 8,
                'messageMinimum' => _('Password is too short. Minimum 8 characters.'),
            ])
        );

        //validate this form for password
        $messages = $validation->validate($request);
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new ServerErrorHttpException((string)$message);
            }
        }

        //Lets find users_invite by hash on our database
        $usersInvite = $this->model::findFirst([
                'conditions' => 'invite_hash = ?0 and is_deleted = 0',
                'bind' => [$hash]
            ]);

        if (!is_object($usersInvite)) {
            throw new NotFoundHttpException('Users Invite not found');
        }

        //Check if user already exists
        $userExists = Users::findFirst([
            'conditions' => 'email = ?0 and is_deleted = 0',
            'bind' => [$usersInvite->email]
        ]);

        if (is_object($userExists)) {
            $newUser = new UsersAssociatedCompany;
            $newUser->users_id = (int)$userExists->id;
            $newUser->companies_id = (int)$usersInvite->companies_id;
            $newUser->identify_id = $usersInvite->role_id;
            $newUser->user_active = 1;
            $newUser->user_role = Roles::existsById((int)$userExists->roles_id)->name;
            if (!$newUser->save()) {
                throw new UnprocessableEntityHttpException((string) current($newUser->getMessages()));
            }
        } else {
            $newUser = new Users();
            $newUser->firstname = $request['firstname'];
            $newUser->lastname = $request['lastname'];
            $newUser->displayname = $request['displayname'];
            $newUser->password = $password;
            $newUser->email = $usersInvite->email;
            $newUser->user_active = 1;
            $newUser->roles_id = $usersInvite->role_id;
            $newUser->created_at = date('Y-m-d H:m:s');
            $newUser->default_company = $usersInvite->companies_id;
            $newUser->default_company_branch = $usersInvite->company->branch->getId();

            try {
                $this->db->begin();

                //signup
                $newUser->signup();

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollback();

                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        }

        //Lets login the new user
        $authInfo = $this->loginUsers($usersInvite->email, $password);

        if (!defined('API_TESTS')) {
            $usersInvite->is_deleted = 1;
            $usersInvite->update();

            return $this->response($authInfo);
        }

        return $this->response($newUser);
    }
}
