<?php

namespace Gewaer\Tests\api;

use Phalcon\Security\Random;
use ApiTester;
use Gewaer\Models\AppsPlans;

class UsersInviteCest
{
    /**
     * Get users invite by hash test
     * @param ApiTester
     * @return void
     */
    public function insertInvite(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $userName = $random->base58();

        $testEmail = $userName . '@example.com';

        //reset
        AppsPlans::findFirst(1)->set('users_total', 10);

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/users/invite', [
            'email' => $testEmail,
            'role_id' => 1,
            'dont_send' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);

        $hash = $data['invite_hash'];

        $I->sendPost('/v1/users-invite/' . $hash, [
            'firstname' => 'testFirstsName',
            'lastname' => 'testLastName',
            'displayname' => $userName,
            'password' => 'testpassword',
            'user_active' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $dataInvite = json_decode($response, true);

        $I->assertTrue($dataInvite['email'] == $testEmail);
    }

    /**
     * Get users invite by hash test
     * @param ApiTester
     * @return void
     */
    public function getByHash(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $userName = $random->base58();

        $testEmail = $userName . '@example.com';

        $I->haveHttpHeader('Authorization', $userData->token);

        //Insert a random new users invite
        $I->sendPost('/v1/users/invite', [
            'email' => $testEmail,
            'role_id' => 1,
            'dont_send' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);

        $hash = $data['invite_hash'];

        //Lets get the recently created users invite
        $I->sendGet('/v1/users-invite/' . $hash);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);
    }
}
