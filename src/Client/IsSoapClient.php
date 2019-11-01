<?php

namespace Svyaznoy\Bundle\AuthBundle\Client;


use SoapClient;
use stdClass;
use Svyaznoy\Bundle\AuthBundle\DTO\AccessTokenValidationResponseDTO;
use Svyaznoy\Bundle\AuthBundle\DTO\IsClientApplicationDTO;
use const SOAP_1_2;
use const WSDL_CACHE_MEMORY;

/**
 * @method stdClass findOAuthConsumerIfTokenIsValid(array $request)
 */
class IsSoapClient extends SoapClient
{
    public function __construct(
        string $wsdl,
        string $endpoint,
        string $login,
        string $password,
        string $timeout,
        array $options = null
    ) {
        $options = $options ?? [];

        $options += [
            'soap_version' => SOAP_1_2,
            'location' => $endpoint,
            'uri' => 'http://org.apache.axis2/xsd',
            'exceptions' => true,
            'login' => $login,
            'password' => $password,
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer_name' => false,
                        'verify_peer' => false,
                        'allow_self_signed' => true,
                    ],
                    'http' => [
                        'timeout' => $timeout,
                    ],
                ]
            ),
            'classmap' => [
                'OAuth2TokenValidationResponseDTO' => AccessTokenValidationResponseDTO::class,
                'OAuth2ClientApplicationDTO' => IsClientApplicationDTO::class,
            ],
        ];

        parent::__construct($wsdl, $options);
    }

    public function getAccessToken(string $identifier): IsClientApplicationDTO
    {
        $request = [
            'validationReqDTO' => [
                'accessToken' => [
                    'identifier' => $identifier,
                    'tokenType' => 'bearer',
                ],
            ],
        ];

        return $this->findOAuthConsumerIfTokenIsValid($request)->return;
    }
}
