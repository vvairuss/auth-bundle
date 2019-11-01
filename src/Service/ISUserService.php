<?php

namespace Svyaznoy\Bundle\AuthBundle\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use RuntimeException;
use Svyaznoy\Bundle\AuthBundle\Exception\BadPhoneFormatException;
use Svyaznoy\Bundle\AuthBundle\Exception\ISUnauthorizedException;
use Svyaznoy\Bundle\AuthBundle\Exception\ISUserExistException;
use Svyaznoy\Bundle\AuthBundle\Exception\ISUserInccorectDataException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ISUserService
 * @package Svyaznoy\Bundle\AuthBundle\Service
 */
class ISUserService
{
    /** @var string */
    protected $userEndpoint;

    /** @var int */
    protected $timeout;

    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $adminLogin;

    /** @var string */
    private $adminPassword;

    public function __construct(
        string $userEndpoint,
        ClientInterface $client,
        int $timeout = 5
    ) {
        $this->userEndpoint = $userEndpoint;
        $this->client = $client;
        $this->timeout = $timeout;
    }

    /**
     * Метод устанавливает логин и пароль администратора IS. Они будут передаваться в запросах.
     * @param $adminLogin
     * @param $adminPassword
     */
    public function setCredentials($adminLogin, $adminPassword)
    {
        $this->adminLogin = $adminLogin;
        $this->adminPassword = $adminPassword;
    }

    /**
     * @param string $mobilePhone - мобильный телефон пользователя
     * @param string $ISLogin     - логин пользователя в IS
     * @param string $password    - пароль для пользователя
     *
     * @return string - UUID
     * @throws GuzzleException
     *
     * @throws RuntimeException
     */
    public function create(string $mobilePhone, string $ISLogin, string $password = ''): string
    {
        $response = $this->sendRequest(
            'POST',
            $this->userEndpoint,
            $this->getUserData($mobilePhone, $ISLogin, $password)
        );
        $result = json_decode($response->getBody());

        if (!isset($result->id)) {
            throw new RuntimeException('Missing UUID - user not created');
        }

        return $result->id;
    }

    /**
     * @param string $uid
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function get($uid)
    {
        $response = $this->sendRequest('GET', "{$this->userEndpoint}/{$uid}");
        $result = json_decode($response->getBody());

        if (!isset($result->id)) {
            // @todo: NotFoundUserException
            throw new RuntimeException('Missing UUID - user not created');
        }

        return $result;
    }

    /**
     * @param string $uid
     *
     * @param string $mobilePhone
     * @param string $ISLogin
     * @param string $password
     *
     * @return bool
     * @throws GuzzleException
     */
    public function update(string $uid, string $mobilePhone, string $ISLogin, string $password = ''): bool
    {
        $response = $this->sendRequest(
            'PUT',
            "{$this->userEndpoint}/{$uid}",
            $this->getUserData($mobilePhone, $ISLogin, $password)
        );
        $result = json_decode($response->getBody());

        if (!isset($result->id)) {
            throw new RuntimeException('Missing UUID - user not updated');
        }

        return true;
    }

    /**
     * @param string $uid
     *
     * @return bool
     * @throws GuzzleException
     */
    public function delete($uid)
    {
        $this->sendRequest('DELETE', "{$this->userEndpoint}/{$uid}");

        return true;
    }

    /**
     * Метод возвращает массив нужного формата для IS с данными пользователя
     *
     * @param string $mobilePhone - мобильный телефон пользователя
     * @param string $ISLogin - логин пользователя в IS
     * @param string $password - пароль для пользователя
     *
     * @throws BadPhoneFormatException
     *
     * @return array
     */
    private function getUserData(string $mobilePhone, string $ISLogin, string $password): array
    {
        if (!preg_match('/^(?>8|\+7)?(?<phone>9\d{9})$/', $mobilePhone, $matches)) {
            throw new BadPhoneFormatException($mobilePhone . ' - некорректный формат номера. Пример правильного формата: 9203243322');
        }
        $userData = [
            'ims' => $matches['phone'],
            'userName' => $ISLogin,
        ];
        if ($password) {
            $userData['password'] = $password;
        }

        return $userData;
    }

    /**
     * Метод оптравляет запрос в IS и возвращает результат
     *
     * @param $method
     * @param $endpoint
     * @param $data
     *
     * @return mixed
     *
     * @throws RequestException|ClientException|ServerException;
     * @throws GuzzleException
     */
    private function sendRequest(string $method, string $endpoint, array $data = null)
    {
        if (!$this->adminLogin || !$this->adminPassword) {
            throw new RuntimeException('Auth login or auth password not seted');
        }

        $requestOptions = [
            'verify' => false,
            'timeout' => $this->timeout,
            'headers' => [
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
            'auth' => [
                $this->adminLogin,
                $this->adminPassword
            ],
        ];

        if (null !== $data) {
            $requestOptions['json'] = $data;
        }
       
        try {
            $response = $this->client->request(
                $method,
                $endpoint,
                $requestOptions
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            switch ($statusCode) {
                case Response::HTTP_UNAUTHORIZED:
                    throw new ISUnauthorizedException(
                        $e->getMessage(),
                        $e->getRequest(),
                        $e->getResponse(),
                        $e
                    );
                    break;
                case Response::HTTP_CONFLICT:
                    throw new ISUserExistException(
                        $e->getMessage(),
                        $e->getRequest(),
                        $e->getResponse(),
                        $e
                    );
                    break;
                case Response::HTTP_BAD_REQUEST:
                    throw new ISUserInccorectDataException(
                        $e->getMessage(),
                        $e->getRequest(),
                        $e->getResponse(),
                        $e
                    );
                    break;
                default:
                    throw new ClientException(
                        $e->getMessage(),
                        $e->getRequest(),
                        $e->getResponse(),
                        $e
                    );
                    break;
            }
        } catch (ServerException $e) {
            $message = 'Ошибка сервера авторизации';

            throw new ServerException(
                $message,
                $e->getRequest(),
                $e->getResponse(),
                $e
            );
        }

        return $response;
    }
}
