<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Svyaznoy\Bundle\AuthBundle\Authenticator\AccessToken;
use Svyaznoy\Bundle\AuthBundle\Client\IsSoapClient;

class IsTokenRepository
{
    private $soapClient;
    private $oauthTokenUrl;

    public function __construct(
        IsSoapClient $soapClient,
        string $oauthTokenUrl
    ) {
        $this->soapClient = $soapClient;
        $this->oauthTokenUrl = $oauthTokenUrl;
    }

    /**
     * Метод отправляет Bear $identifier токен в SOAP сервис IS и возвращает объект AccessToken с подробной
     * информацией о токине.
     *
     * @param string $identifier - access_token
     *
     * @return AccessToken
     */
    public
    function find(
        string $identifier
    ): ?AccessToken {
        $response = $this->soapClient->getAccessToken($identifier)->getAccessTokenValidationResponse();

        if ($response->hasError()) {
            return null;
        }

        return new AccessToken(
            [
                'access_token' => $identifier,
                'resource_owner_id' => $response->getAuthorizedUser(),
                'expires' => $response->getExpiryTime(),
            ]
        );
    }

    /**
     * @param       $clientId
     * @param       $clientSecret
     * @param array $body
     *
     * @return AccessToken
     * @throws GuzzleException
     */
    protected function getToken(string $clientId, string $clientSecret, array $body): AccessToken
    {
        $guzzleClient = new Client();
        try {
            $response = $guzzleClient->request(
                'POST',
                $this->oauthTokenUrl,
                [
                    'verify' => false,
                    'timeout' => 60,
                    'headers' => [
                        'Connection' => 'close',
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    ],
                    'auth' => [
                        $clientId,
                        $clientSecret,
                    ],
                    'form_params' => $body,
                ]
            )->getBody();
        } catch (BadResponseException $e) {
            throw new RuntimeException('token request failed');
        }

        return new AccessToken(json_decode($response, true));
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $login
     * @param string $password
     *
     * @return AccessToken
     * @throws GuzzleException
     * @throws Exception
     */
    public function getTokenByUser(string $clientId, string $clientSecret, string $login, string $password)
    {
        return $this->getToken(
            $clientId,
            $clientSecret,
            [
                'scope' => sprintf('rnd_%08d', random_int(1, 99999999)),
                'grant_type' => 'password',
                'response_type' => 'token',
                'username' => $login,
                'password' => $password,
            ]
        );
    }
}
