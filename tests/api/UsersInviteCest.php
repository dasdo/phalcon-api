<?php 

class UsersInviteCest
{
    public function insertInvite(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $testEmail = 'testMC@example.com';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/users/invite', [
            'email' => $testEmail,
            'role' => 'Admins',
            'dont_send' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);

        $hash = $data['invite_hash'];

        $I->sendPost('/v1/user-invites/' . $hash, [
            'firstname' => 'testFirstName',
            'lastname' => 'testLastName',
            'displayname' => 'testDisplayName',
            'password' => 'testpassword',
            'user_active' => 1
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $dataInvite = json_decode($response, true);

        $I->assertTrue($dataInvite['email'] == $testEmail);
    }
}
