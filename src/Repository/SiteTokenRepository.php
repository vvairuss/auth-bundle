<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use RuntimeException;
use Svyaznoy\Bundle\AuthBundle\Authenticator\AccessToken;
use Svyaznoy\Bundle\AuthBundle\Entity\User;
use Svyaznoy\Bundle\AuthBundle\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class SiteTokenRepository
{
    private $tokenUrl;
    private $tokenInfoUrl;
    private $proxy;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        string $tokenUrl,
        string $tokenInfoUrl,
        string $proxy
    ) {
        $this->userRepository = $userRepository;
        $this->tokenUrl = $tokenUrl;
        $this->tokenInfoUrl = $tokenInfoUrl;
        $this->proxy = $proxy;
    }

    public function find(string $identifier): ?AccessToken
    {
        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                $this->tokenInfoUrl,
                [
                    'verify' => false,
                    'timeout' => 60,
                    'proxy' => $this->proxy,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Connection' => 'close',
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    ],
                    'form_params' => [
                        'token' => $identifier,
                    ],
                ]
            )->getBody();
            $result = json_decode($response, true);

            /** @var User $user */
            $user = $this->userRepository->findOneBy([
                'siteUid' => $result['user_id'],
            ]);
        } catch (BadResponseException $e) {
            return null;
        }

        return new AccessToken(
            [
                'access_token' => $result['access_token'],
                'resource_owner_id' => $user->getLogin(),
                'expires' => $result['expires'],
            ]
        );
    }

    public function getTokenByUser(string $clientId, string $clientSecret, string $login, string $password): AccessToken
    {
        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                $this->tokenUrl,
                [
                    'verify' => false,
                    'timeout' => 60,
                    'proxy' => $this->proxy,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Connection' => 'close',
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    ],
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $login,
                        'password' => $password,
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                    ],
                ]
            )->getBody();
        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode === Response::HTTP_UNAUTHORIZED) {
                throw new UnauthorizedException(
                    $e->getMessage(),
                    $e->getRequest(),
                    $e->getResponse(),
                    $e
                );
            }

            throw new RuntimeException('token request failed');
        }

        return new AccessToken(json_decode($response, true));
    }

    public function getClientToken($login, $password)
    {
        $client = new Client();
        $response = $client->post(
            $this->tokenUrl,
            [
                'proxy' => $this->proxy,
                'headers' => [
                    'Accept' => 'application/json',
                    'Connection' => 'close',
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $login,
                    'client_secret' => $password,
                ],
            ]
        );

        $result = json_decode($response->getBody(), true);

        return "${result['token_type']} ${result['access_token']}";
    }
}