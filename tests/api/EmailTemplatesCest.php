<?php 

class EmailTemplatesCest
{
    /**
     * Model
     */
    protected $model = 'email-templates';

    /**
     * Create a new Email Templates
     *
     * @param ApiTester $I
     * @return void
     */
    public function insertTemplate(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $testName = 'Test';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . $this->model, [
            'users_id' => 2,
            'company_id' => 2,
            'app_id' => 1,
            'name' => 'Test',
            'template' => 'Hello!!! This is a test email template',
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['name'] == $testName);
    }

    /**
     * update a Email Template
     *
     * @param ApiTester $I
     * @return void
     */
    public function updateTemplate(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $updatedName = 'Updated Test Name';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/' . $this->model);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendPUT('/v1/' . $this->model . '/' . $data[count($data) - 1]['id'], [
            'name' => $updatedName
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['name'] == $updatedName);
    }
}
