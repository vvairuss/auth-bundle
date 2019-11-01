<?php

namespace Svyaznoy\Bundle\AuthBundle\DTO;


class IsClientApplicationDTO
{
    /**
     * @var AccessTokenValidationResponseDTO
     */
    private $accessTokenValidationResponse;
    private $consumerKey;

    public function getAccessTokenValidationResponse(): AccessTokenValidationResponseDTO
    {
        return $this->accessTokenValidationResponse;
    }

    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

}
