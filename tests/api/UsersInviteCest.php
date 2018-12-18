<?php

namespace Gewaer\Tests\api;

use Phalcon\Security\Random;
use ApiTester;

class UsersInviteCest
{
    public function insertInvite(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $userName = $random->base58();

        $testEmail = $userName . '@example.com';

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
}
