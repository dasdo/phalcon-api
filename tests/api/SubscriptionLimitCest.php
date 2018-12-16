<?php

namespace Gewaer\Tests\api;

use Phalcon\Security\Random;
use Gewaer\Models\UserCompanyAppsActivities;
use Gewaer\Models\AppsPlans;
use ApiTester;
use Gewaer\Exception\SubscriptionPlanLimitException;

class SubscriptionLimitCest
{
    /**
     * Confirm working with a system model update its total activity for the app and company the
     * users is working with
     *
     * @param ApiTester $I
     * @return void
     */
    public function updateActivity(ApiTester $I): void
    {
        //we are going to test the activity of 1 modele (users)

        //first we need to invite a new user to the current company
        $userData = $I->apiLogin();
        $random = new Random();
        $userName = $random->base58();

        $testEmail = $userName . '@example.com';

        $I->haveHttpHeader('Authorization', $userData->token);

        //set limit to 10 so we can fail
        $appPlanSettings = AppsPlans::findFirst(1)->set('users_total', 10);

        //get current total user activity
        $totalUserActivities = UserCompanyAppsActivities::get('users_total');

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

        //now after inviting a new user the total users for this app company should have increased
        $I->assertTrue(UserCompanyAppsActivities::get('users_total') > $totalUserActivities);
    }

    /**
     * Confirm by chaging the total usage of the plan for the test account, we encounter the limit exception
     *
     * @param ApiTester $I
     * @return void
     */
    public function isAtLimit(ApiTester $I):void
    {
        //we are going to test the activity of 1 modele (users)

        //first we need to invite a new user to the current company
        $userData = $I->apiLogin();
        $random = new Random();
        $userName = $random->base58();

        $testEmail = $userName . '@example.com';

        $I->haveHttpHeader('Authorization', $userData->token);

        //set limit to 1 so we can fail
        $appPlanSettings = AppsPlans::findFirst(1)->set('users_total', 1);

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
        $reachLimit = false;

        try {
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
        } catch (SubscriptionPlanLimitException $e) {
            $reachLimit = true;
        }

        //are we at our limit?
        $I->assertTrue($reachLimit);
    }
}
