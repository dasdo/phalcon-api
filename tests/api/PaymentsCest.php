<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Phalcon\Security\Random;

class PaymentsCest
{
    protected $model = 'payments';

    /**
     * Pending Payment
     *
     * @param ApiTester $I
     * @return void
     */
    public function pendingPayment(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $localeName = $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . 'webhook/' . $this->model, [
            'type' => 'charge.pending',
            'data' => [
                'object' => [
                    'customer' => 'cus_ETq3Zj0KbykfIr'
                ]
            ]
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(current($data) == 'Webhook Handled');
    }

    /**
     * Failed Payment
     *
     * @param ApiTester $I
     * @return void
     */
    public function failedPayment(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $localeName = $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . 'webhook/' . $this->model, [
            'type' => 'charge.failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_ETq3Zj0KbykfIr'
                ]
            ]
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(current($data) == 'Webhook Handled');
    }

    /**
     * Successful Payment
     *
     * @param ApiTester $I
     * @return void
     */
    public function SucceededPayment(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $localeName = $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . 'webhook/' . $this->model, [
            'type' => 'charge.succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_ETq3Zj0KbykfIr'
                ]
            ]
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(current($data) == 'Webhook Handled');
    }
}
