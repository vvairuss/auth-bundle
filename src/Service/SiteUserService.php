<?php

namespace Svyaznoy\Bundle\AuthBundle\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Svyaznoy\Bundle\AuthBundle\Exception\BadPhoneFormatException;
use Svyaznoy\Bundle\AuthBundle\Repository\SiteTokenRepository;

class SiteUserService
{
    /** @var string */
    private $endpoint;

    /** @var string */
    private $findByEmailEndpoint;

    /** @var SiteTokenRepository */
    private $tokenRepository;

    /** @var ClientInterface ClientInterface */
    private $client;

    /** @var string */
    private $proxy;

    /** @var string */
    private $adminLogin;

    /** @var string */
    private $adminPassword;

    public function __construct(
        string $endpoint,
        string $findByEmailEndpoint,
        string $proxy,
        SiteTokenRepository $tokenRepository,
        ClientInterface $client
    ) {
        $this->endpoint = $endpoint;
        $this->findByEmailEndpoint = $findByEmailEndpoint;
        $this->proxy = $proxy;
        $this->tokenRepository = $tokenRepository;
        $this->client = $client;
    }

    /**
     * Метод устанавливает логин и пароль администратора IS. Они будут передаваться в запросах.
     * @param $adminLogin
     * @param $adminPassword
     */
    public function setCredentials(string $adminLogin, string $adminPassword): void
    {
        $this->adminLogin = $adminLogin;
        $this->adminPassword = $adminPassword;
    }

    /**
     * @param string $mobilePhone - мобильный телефон пользователя
     * @param string $login       - логин пользователя в IS
     * @param string $password    - пароль для пользователя
     * @param string $firstName   - имя пользователя
     *
     * @return int - UUID
     * @throws GuzzleException
     *
     * @throws RuntimeException
     */
    public function create(string $mobilePhone, string $login, string $password = '', string $firstName = ''): int
    {
        $response = $this->sendRequest(
            'POST',
            sprintf($this->endpoint, 'register'),
            [
                'email' => $login,
                'password' => $password,
                'first_name' => $firstName,
                'phone' => $this->getPhone($mobilePhone),
            ]
        );
        $result = json_decode($response->getBody());

        if (!isset($result->user_id)) {
            throw new RuntimeException('Missing UUID - user not created');
        }

        return $result->user_id;
    }

    /**
     * @param string $uid
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function get($uid)
    {
        $response = $this->sendRequest('GET', sprintf($this->endpoint, $uid));
        $result = json_decode($response->getBody());

        if (!isset($result->id)) {
            // @todo: NotFoundUserException
            throw new RuntimeException('Missing UUID - user not created');
        }

        return $result;
    }

    /**
     * @param int $uid
     *
     * @param string $mobilePhone
     * @param string $ISLogin
     *
     * @return bool
     * @throws GuzzleException
     */
    public function update(int $uid, string $mobilePhone, string $ISLogin): bool
    {
        $response = $this->sendRequest(
            'PUT',
            sprintf($this->endpoint, $uid),
            [
                'phone' => $this->getPhone($mobilePhone),
            ]
        );
        $result = json_decode($response->getBody());

        if ($result->success !== true) {
            throw new RuntimeException('User not updated');
        }

        return true;
    }

    /**
     * @param int $uid
     *
     * @param string $newPassword
     * @param string $currentPassword
     *
     * @return bool
     * @throws GuzzleException
     */
    public function changePassword(int $uid, string $newPassword, string $currentPassword): bool
    {
        $response = $this->sendRequest(
            'PUT',
            sprintf($this->endpoint, $uid),
            [
                'password' => $newPassword,
                'current_password' => $currentPassword,
            ]
        );
        $result = json_decode($response->getBody());

        if ($result->success !== true) {
            throw new RuntimeException('User not updated');
        }

        return true;
    }

    public function delete($uid)
    {
    }

    public function findByEmail($email): int
    {
        $response = $this->sendRequest(
            'GET',
            sprintf($this->findByEmailEndpoint, $email)
        );
        $result = json_decode($response->getBody());

        return $result->id;
    }

    /**
     * @param string $phone - мобильный телефон пользователя
     *
     * @return array
     *@throws BadPhoneFormatException
     *
     */
    private function getPhone(string $phone): string
    {
        if (!preg_match('/^(?>8|\+7)?(?<phone>9\d{9})$/', $phone, $matches)) {
            throw new BadPhoneFormatException($phone . ' - некорректный формат номера. Пример правильного формата: 9203243322');
        }

        return $matches['phone'];
    }

    private function sendRequest(string $method, string $endpoint, array $data = null): ResponseInterface
    {
        $token = $this->tokenRepository->getClientToken(
            $this->adminLogin,
            $this->adminPassword
        );

        $requestOptions = [
            'proxy' => $this->proxy,
            'headers' => [
                'Authorization' => $token,
                'Accept' => 'application/json',
            ]
        ];

        if ($data !== null) {
            $requestOptions['form_params'] = $data;
        }

        $response = $this->client->request(
            $method,
            $endpoint,
            $requestOptions
        );

        return $response;
    }
}
