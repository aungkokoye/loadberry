<?php
namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use App\Entity\User;

class LoginSuccessListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LoginSuccessListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function LoginSuccessEvent(AuthenticationSuccessEvent $event)
    {
        // Get the User entity.
       /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $user->setLastLoginFailureAt(null);
            $user->setLoginFailure(0);
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}