<?php
namespace App\EventListener;

use App\Service\LoginMaxAttendService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use App\Entity\User;

class LoginFailureListener
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * LoginFailureListener constructor.
     * @param EntityManagerInterface $em
     * @param RequestStack           $requestStack
     * @param SessionInterface       $session
     */
    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, SessionInterface $session)
    {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest()->request;
        $this->session = $session;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function LoginFailureEvent(AuthenticationFailureEvent $event)
    {
        $email = $this->request->get('email');
        if (!empty($email)) {
            $result = $this->em->getRepository('App:User')->findByEmail($email);
            if (!empty($result)) {
                /** @var User $user */
                foreach($result as $user) {
                    $loginFailure = $user->getLoginFailure();
                    if (LoginMaxAttendService::MAX_LOG_IN_FAILURE < $loginFailure) {
                        $this->session->set(LoginMaxAttendService::LOG_IN_FAILURE_SESSION, $user->getLastLoginFailureAt()->getTimestamp());
                    }
                    $user->setLastLoginFailureAt(new \DateTime());
                    $user->setLoginFailure($loginFailure + 1);
                    $this->em->persist($user);
                    $this->em->flush();
                }
            }
        }
    }
}