<?php

namespace App\Service;


use App\EventListener\LoginFailureListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginMaxAttendService
{
    const DELAY_MINUTES = 5;
    const MAX_LOG_IN_FAILURE = 3;
    const LOG_IN_FAILURE_SESSION = 'log_in_failure_session';

    /**
     * @var SessionInterface
     */
    private  $session;

    /**
     * @var EntityManagerInterface
     */
    private  $em;

    public function __construct(SessionInterface $session, EntityManagerInterface  $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function check() :bool
    {
        $lastLoginFailureTimeStamp = $this->session->get(self::LOG_IN_FAILURE_SESSION, null);

        if(!empty($lastLoginFailureTimeStamp)) {
            $lastLoginFailure = new \DateTime();
            $lastLoginFailure->setTimestamp($lastLoginFailureTimeStamp);
            $currentDateTime = new \DateTime();

            // Add 5 minutes to last login failure
            $lastLoginFailure->add(new \DateInterval('PT' . self::DELAY_MINUTES . 'M'));

            if ($currentDateTime < $lastLoginFailure) {
                return false;
            }
            $this->session->set(self::LOG_IN_FAILURE_SESSION, null);
        }

        return true;
    }

}