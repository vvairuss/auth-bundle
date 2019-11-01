<?php

namespace Svyaznoy\Bundle\AuthBundle\Authenticator;

use Svyaznoy\Bundle\AuthBundle\Repository\IsTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class IsAuthenticator extends AbstractGuardAuthenticator
{
    private $isTokenRepository;

    public function __construct(IsTokenRepository $isTokenRepository)
    {
        $this->isTokenRepository = $isTokenRepository;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): AccessToken
    {
        preg_match(
            '/(?<=Bearer\s)(?<token>\w+)/',
            $request->headers->get('Authorization'),
            $token
        );

        $tokenIS = $this->isTokenRepository->find($token['token']);

        if (!$tokenIS) {
            throw new TokenNotFoundException();
        }

        return $tokenIS;
    }

    /**
     * @param                       $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $username = str_replace('@carbon.super', '', $credentials->getResourceOwnerId());
        return $userProvider->loadUserByUsername($username);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
