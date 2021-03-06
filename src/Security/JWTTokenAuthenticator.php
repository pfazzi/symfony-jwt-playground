<?php
declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class JWTTokenAuthenticator extends BaseAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $webLoginRoute;

    /**
     * @var string
     */
    private $webFirewallName;

    public function __construct(
        RouterInterface $router,
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        TokenExtractorInterface $tokenExtractor,
        string $webLoginRoute,
        string $webFirewallName
    )
    {
        parent::__construct($jwtManager, $dispatcher, $tokenExtractor);

        $this->router = $router;
        $this->webLoginRoute = $webLoginRoute;
        $this->webFirewallName = $webFirewallName;
    }

    public function supports(Request $request)
    {
        if(!parent::supports($request)) {
            return false;
        }

        try {
            parent::getCredentials($request);
        } catch (InvalidTokenException $exception) {
            return false;
        }

        return true;
    }


    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->redirectToLoginPage($request);
    }

    private function redirectToLoginPage(Request $request): Response
    {
        $this->saveTargetPath(
            $request->getSession(),
            $this->webFirewallName,
            $request->getUri()
        );

        return new RedirectResponse($this->router->generate($this->webLoginRoute));
    }
}