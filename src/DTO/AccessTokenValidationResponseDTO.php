<?php

namespace Svyaznoy\Bundle\AuthBundle\DTO;

class AccessTokenValidationResponseDTO
{
    /**
     * @var string|null
     */
    private $authorizedUser;
    private $errorMsg;

    /**
     * @var int
     */
    private $expiryTime;

    public function getAuthorizedUser(): ?string
    {
        return $this->authorizedUser;
    }

    public function getExpiryTime(): int
    {
        return $this->expiryTime;
    }

    public function hasError(): bool
    {
        return (bool)$this->errorMsg;
    }
}
