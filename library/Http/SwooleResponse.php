<?php
// +----------------------------------------------------------------------
// | SwooleResponse.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------

namespace Gewaer\Http;

use Phalcon\Http\Cookie;
use Phalcon\Http\Response as PhResponse;
use swoole_http_response;
use Exception;

class SwooleResponse extends Response
{
    protected $response;

    public function init(swoole_http_response $response)
    {
        $this->response = $response;
        $this->_sent = false;
        $this->_content = null;
        $this->setStatusCode(200);
    }

    public function send(): PhResponse
    {
        if ($this->_sent) {
            throw new Exception('Response was already sent');
        }

        $this->_sent = true;
        // 处理Headers
        $headers = $this->getHeaders();
        foreach ($headers->toArray() as $key => $val) {
            $this->response->header($key, $val);
        }

        /** @var Cookies $cookies */
        $cookies = $this->getCookies();
        if ($cookies) {
            /** @var Cookie $cookie */
            foreach ($cookies->getCookies() as $cookie) {
                $this->response->cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiration(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->getSecure(),
                    $cookie->getHttpOnly()
                );
            }
        }

        // 处理
        $this->response->status($this->getStatusCode());
        $this->response->end($this->_content);
        
        //reest di
        $this->_sent = false;
        $this->getDi()->reset();

        return $this;
    }
}
