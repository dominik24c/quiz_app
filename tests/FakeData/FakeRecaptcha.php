<?php


namespace App\Tests\FakeData;


use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;

class FakeRecaptcha extends ReCaptcha
{
    public function __construct(string $secret="str")
    {
        parent::__construct($secret);
    }

    public function verify($response, $remoteIp = null)
    {
        return new Response(true);
    }


}
