<?php

namespace App\Controller;

use App\Service\LoginMaxAttendService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils   $authenticationUtils
     * @param LoginMaxAttendService $loginservice
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, LoginMaxAttendService $loginservice): Response
    {
        if(!$loginservice->check()) {
            $this->addFlash('error', 'Max Login attempts reached. Please wait for 5 minutes and try again.');
            return $this->redirectToRoute('index');
        }

         if ($this->getUser()) {
             return $this->redirectToRoute('panel');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
