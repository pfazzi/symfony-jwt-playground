<?php
declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebController extends AbstractController
{
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function securedOneAction(Request $request)
    {
        return Response::create('<body><h1>Secured route #1</h1></body>');
    }

    public function securedTwoAction(Request $request)
    {
        return Response::create('<body><h1>Secured route #2</h1></body>');
    }

    public function unsecuredAction(Request $request)
    {
        return Response::create('<body><h1>Unsecured route</h1></body>');
    }

    public function indexAction(Request $request)
    {
        return Response::create('<body><h1>Index</h1></body>');
    }
}