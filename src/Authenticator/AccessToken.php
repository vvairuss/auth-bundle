<?php

namespace Svyaznoy\Bundle\AuthBundle\Authenticator;

use InvalidArgumentException;
use RuntimeException;

/**
 * Represents an access token.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-1.4 Access Token (RFC 6749, §1.4)
 */
class AccessToken
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var string
     */
    protected $resourceOwnerId;

    /**
     * @var string
     */
    protected $tokenType;

    /**
     * @var int
     */
    protected $expires;

    /**
     * @var int
     */
    protected $expiresIn;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Constructs an access token.
     *
     * @param array $options An array of options returned by the service provider
     *     in the access token request. The `access_token` option is required.
     * @throws InvalidArgumentException if `access_token` is not provided in `$options`.
     */
    public function __construct(array $options = [])
    {
        if (empty($options['access_token'])) {
            throw new InvalidArgumentException('Required option not passed: "access_token"');
        }

        $this->accessToken = $options['access_token'];

        if (!empty($options['resource_owner_id'])) {
            $this->resourceOwnerId = $options['resource_owner_id'];
        }

        if (!empty($options['refresh_token'])) {
            $this->refreshToken = $options['refresh_token'];
        }

        // We need to know when the token expires. Show preference to
        // 'expires_in' since it is defined in RFC6749 Section 5.1.
        // Defer to 'expires' if it is provided instead.
        if (isset($options['expires_in'])) {
            if (!is_numeric($options['expires_in'])) {
                throw new InvalidArgumentException('expires_in value must be an integer');
            }

            $this->expires = $options['expires_in'] !== 0 ? time() + $options['expires_in'] : 0;
            $this->expiresIn = $options['expires_in'];
        } elseif (!empty($options['expires'])) {
            // Some providers supply the seconds until expiration rather than
            // the exact timestamp. Take a best guess at which we received.
            $expires = $options['expires'];

            if (!$this->isExpirationTimestamp($expires)) {
                $expires += time();
            }

            $this->expires = $expires;
        }

        // token type
        if (isset($options['token_type'])) {
            $this->tokenType = $options['token_type'];
        }

        // Capture any additional values that might exist in the token but are
        // not part of the standard response. Vendors will sometimes pass
        // additional user data this way.
        $this->values = array_diff_key($options, array_flip([
            'access_token',
            'resource_owner_id',
            'refresh_token',
            'expires_in',
            'expires',
            'token_type'
        ]));
    }

    /**
     * Check if a value is an expiration timestamp or second value.
     *
     * @param integer $value
     * @return bool
     */
    protected function isExpirationTimestamp($value): bool
    {
        // If the given value is larger than the original OAuth 2 draft date,
        // assume that it is meant to be a (possible expired) timestamp.
        $oauth2InceptionDate = 1349067600; // 2012-10-01
        return ($value > $oauth2InceptionDate);
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @inheritdoc
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerId()
    {
        return $this->resourceOwnerId;
    }

    /**
     * @inheritdoc
     */
    public function hasExpired(): bool
    {
        $expires = $this->getExpires();

        if (empty($expires)) {
            throw new RuntimeException('"expires" is not set on the token');
        }

        return $expires < time();
    }

    /**
     * @inheritdoc
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) $this->getToken();
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        $parameters = $this->values;

        if ($this->accessToken) {
            $parameters['access_token'] = $this->accessToken;
        }

        if ($this->refreshToken) {
            $parameters['refresh_token'] = $this->refreshToken;
        }

        if ($this->expires) {
            $parameters['expires'] = $this->expires;
        }

        if ($this->expiresIn) {
            $parameters['expires_in'] = $this->expiresIn;
        }

        if ($this->tokenType) {
            $parameters['token_type'] = $this->expiresIn;
        }

        if ($this->resourceOwnerId) {
            $parameters['resource_owner_id'] = $this->resourceOwnerId;
        }

        return $parameters;
    }
}
