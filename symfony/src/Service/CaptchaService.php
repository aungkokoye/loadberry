<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CaptchaService
 * @package App\Service
 */
class CaptchaService
{

    /**
     * @var ContainerInterface
     */
    private  $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request   $request
     * @return bool
     */
    public function validate(Request $request) : bool
    {
        $url = $this->container->getParameter('google_captcha_url');
        $secretKey = $this->container->getParameter('google_captcha_secret_key');

        $captchaRespond = $request->request->get('g-recaptcha-response');
        $verifyResponse = file_get_contents($url.'?secret='.$secretKey.'&response='.$captchaRespond);
        $responseData = json_decode($verifyResponse);
        $result = $responseData->success;
        if (!$result) {
            $this->container->get('session')->getFlashBag()->add('error', 'Captcha is not valid!');
        }
        return $result;
    }
}