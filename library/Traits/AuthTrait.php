<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Users;
use Baka\Auth\Models\Sessions;

/**
 * Trait ResponseTrait
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property Config $config
 * @property Request $request
 * @property Auth $auth
 * @property \Phalcon\Di $di
 *
 */
trait AuthTrait
{
    /**
     * Login user
     * @param string
     * @return array
     */
    private function loginUsers(string $email, string $password): array
    {
        $userIp = !defined('API_TESTS') ? $this->request->getClientAddress() : '127.0.0.1';

        $random = new \Phalcon\Security\Random();

        $userData = Users::login($email, $password, 1, 0, $userIp);

        $sessionId = $random->uuid();

        //save in user logs
        $payload = [
            'sessionId' => $sessionId,
            'email' => $userData->getEmail(),
            'iat' => time(),
        ];

        $token = $this->auth->make($payload);

        //start session
        $session = new Sessions();
        $session->start($userData, $sessionId, $token, $userIp, 1);

        return [
            'token' => $token,
            'time' => date('Y-m-d H:i:s'),
            'expires' => date('Y-m-d H:i:s', time() + $this->config->jwt->payload->exp),
            'id' => $userData->getId(),
        ];
    }
}
