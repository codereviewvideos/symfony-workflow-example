<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(Router $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return new RedirectResponse(
                $this->router->generate('passport_review')
            );
        }

        return new RedirectResponse(
            $this->router->generate('dashboard')
        );
    }

}