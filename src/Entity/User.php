<?php

namespace Svyaznoy\Bundle\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\MappedSuperclass
 */
class User implements UserInterface, Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sbrLogin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $isUid;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $siteUid;

    /**
     * Имя пользователя
     *
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $firstName;

    /**
     * Фамилия пользователя
     *
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $workPhone;

    /**
     * Тип занятости
     *
     * @var string
     *
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    private $employmentPosition;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    private $region;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean", nullable=false, options={"unsigned":true})
     */
    private $deleted = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false, unique=true, options={"comment":"User email"})
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned":true})
     */
    private $salespointId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=false, options={"comment":"site & SBR pass"})
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $sbrPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('Widget', 'Seller', 'PartnerAdmin', 'PartnerManager')", nullable=true)
     */
    private $type;

    /**
     * @var UserGroup|null
     *
     * @ORM\ManyToOne(targetEntity="Svyaznoy\Bundle\AuthBundle\Entity\UserGroup", inversedBy="users", fetch="EAGER")
     */
    private $group;

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
     * @return int|null
     */
    public function getSiteUid(): ?int
    {
        return $this->siteUid;
    }

    /**
     * @param int $siteUid
     *
     * @return User
     */
    public function setSiteUid(int $siteUid): User
    {
        $this->siteUid = $siteUid;

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
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;

        return $this;
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
