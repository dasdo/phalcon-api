<?php 

class CompaniesCustomFieldsCest
{
    /**
     * Model
     */
    protected $model = 'companies-custom-fields';

    /**
     * Create a new Email Templates
     *
     * @param ApiTester $I
     * @return void
     */
    public function insertCompaniesCustomField(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $testValue = 'test_' . time();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . $this->model, [
            'companies_id' => 3,
            'custom_fields_id' => 1,
            'value' => $testValue
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['value'] == $testValue);
    }

    /**
     * update a Email Template
     *
     * @param ApiTester $I
     * @return void
     */
    public function updateCompaniesCustomField(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $updatedValue = 'Updated Value';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet("/v1/{$this->model}");

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendPUT('/v1/' . $this->model . '/' . $data[count($data) - 1]['id'], [
            'value' => $updatedValue
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['value'] == $updatedValue);
    }
}