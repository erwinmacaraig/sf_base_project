<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/', requirements:['_locale' => 'en|ph'])]
class LoginController extends AbstractController
{
    #[Route('/{_locale}/login', name: 'blog_login')]
    public function index(AuthenticationUtils $authenticationUtils, string $_locale='en'): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // get last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/{_locale}/logout', name: 'blog_logout', methods:['GET', 'POST'])]
    public function logout(string $_locale='en')
    {
        //controller can be blank because Symfony will use its own logout
        throw new Exception('Dont\'t forget to activate logout in security.yaml');
    }
}
