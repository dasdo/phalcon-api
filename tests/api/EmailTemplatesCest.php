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
    public function insertTemplate(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $testName = 'test_' . time();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . $this->model, [
            'users_id' => 3,
            'companies_id' => 3,
            'app_id' => 1,
            'name' => $testName,
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
        $updatedName = 'Updated Test Name 2';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet("/v1/{$this->model}");

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendPUT('/v1/' . $this->model . '/' . $data[count($data) - 1]['id'], [
            'template' => $updatedName
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['template'] == $updatedName);
    }
}
