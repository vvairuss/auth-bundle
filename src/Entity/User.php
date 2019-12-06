<?php

namespace Svyaznoy\Bundle\AuthBundle\Entity;

use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, Serializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $sbrLogin;

    /**
     * @var string
     */
    protected $isUid;

    /**
     * Имя пользователя
     *
     * @var string
     */
    protected $firstName;

    /**
     * Фамилия пользователя
     *
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $mobilePhone;

    /**
     * @var string
     */
    protected $workPhone;

    /**
     * Тип занятости
     *
     * @var string
     */
    protected $employmentPosition;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var int
     */
    protected $deleted = 0;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $salespointId;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $sbrPassword;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var UserGroup|null
     */
    protected $group;

    /**
     * @see \Serializable::serialize()
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->sbrLogin,
                $this->email,
            ]
        );
    }

    /**
     * @see \Serializable::unserialize()
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->sbrLogin,
            $this->email,
            ) = unserialize($serialized);
    }
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSbrLogin(): ?string
    {
        return $this->sbrLogin;
    }

    /**
     * @param string $sbrLogin
     *
     * @return User
     */
    public function setSbrLogin(string $sbrLogin): User
    {
        $this->sbrLogin = $sbrLogin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsUid(): ?string
    {
        return $this->isUid;
    }

    /**
     * @param string $isUid
     *
     * @return User
     */
    public function setIsUid(string $isUid): User
    {
        $this->isUid = $isUid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return User
     */
    public function setMiddleName(string $middleName): User
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     *
     * @return User
     */
    public function setMobilePhone(string $mobilePhone): User
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    /**
     * @param string $workPhone
     *
     * @return User
     */
    public function setWorkPhone(string $workPhone): User
    {
        $this->workPhone = $workPhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmploymentPosition(): ?string
    {
        return $this->employmentPosition;
    }

    /**
     * @param string $employmentPosition
     *
     * @return User
     */
    public function setEmploymentPosition(string $employmentPosition): User
    {
        $this->employmentPosition = $employmentPosition;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string $region
     *
     * @return User
     */
    public function setRegion(string $region): User
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDeleted(): ?int
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     *
     * @return User
     */
    public function setDeleted(int $deleted): User
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function setLogin(string $login): User
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSalespointId(): ?int
    {
        return $this->salespointId;
    }

    /**
     * @param int $salespointId
     *
     * @return User
     */
    public function setSalespointId(int $salespointId): User
    {
        $this->salespointId = $salespointId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSbrPassword(): ?string
    {
        return $this->sbrPassword;
    }

    /**
     * @param string $sbrPassword
     *
     * @return User
     */
    public function setSbrPassword(string $sbrPassword): User
    {
        $this->sbrPassword = $sbrPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return User
     */
    public function setType(?string $type): User
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getGroup() {
        return $this->group;
    }

    public function setGroup(?UserGroup $group) {
        $this->group = $group;
        return $this;
    }
}
