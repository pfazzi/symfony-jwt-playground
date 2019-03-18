<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * AuthenticationSuccessHandler.
 *
 * @author Dev Lexik <dev@lexik.fr>
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $providerKey = 'jwt_provider';

    /**
     * @param JWTTokenManagerInterface $jwtManager
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface $router
     */
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router
    )
    {
        $this->jwtManager = $jwtManager;
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $jwt = $this->jwtManager->create($user = $token->getUser());

        $targetPath = $this->getTargetPath($request->getSession(), $this->providerKey) ??
            $this->router->generate('web_index');

        $response = new RedirectResponse($targetPath);
        $event    = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);

        $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);

        $response->headers->setCookie(new Cookie('jwt', $jwt));

        return $response;
    }
}
