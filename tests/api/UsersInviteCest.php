<?php

namespace Gewaer\Tests\api;

use Phalcon\Security\Random;
use ApiTester;
use Gewaer\Models\AppsPlans;

class UsersInviteCest
{
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
            'role' => 'Canvas.Admins',
            'dont_send' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);

        $hash = $data['invite_hash'];

        $I->sendPost('/v1/user-invites/' . $hash, [
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
}
