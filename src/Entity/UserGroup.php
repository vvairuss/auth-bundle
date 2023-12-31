<?php

namespace Svyaznoy\Bundle\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="user_group")
 * @ORM\Entity(repositoryClass="Svyaznoy\Bundle\AuthBundle\Repository\UserGroupRepository")
 */
class UserGroup
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
     * Название группы
     *
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Svyaznoy\Bundle\AuthBundle\Entity\User",
     *     mappedBy="group",
     *     fetch="LAZY",
     *     cascade={"persist"}
     * )
     */
    private $users;

    /**
     * @var ArrayCollection|AccessRight[]
     *
     * @ORM\ManyToMany(targetEntity="Svyaznoy\Bundle\AuthBundle\Entity\AccessRight")
     * @ORM\JoinTable(name="access_right_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="access_right_id", referencedColumnName="id")}
     * )
     */
    private $accessRights;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean", nullable=false, options={"unsigned":true})
     */
    private $deleted = 0;

    /**
     * UserGroup constructor.
     *
     * @param                 $name
     * @param Collection $userCollection
     * @param Collection $rightCollection
     */
    public function __construct($name, Collection $userCollection, Collection $rightCollection)
    {
        $this->name = $name;
        $this->setUsers($userCollection);
        $this->setAccessRights($rightCollection);
    }

    public function getAccessRights(): Collection {
        return $this->accessRights;
    }

    /**
     * @param AccessRight[]|Collection $accessRights
     *
     * @return UserGroup
     */
    public function setAccessRights($accessRights)
    {
        $this->accessRights = $accessRights;

        return $this;
    }

    public function addAccessRights(AccessRight $accessRight)
    {
        if (!$this->getAccessRights()->contains($accessRight)) {
            $this->accessRights->add($accessRight);
        }
        return $this;
    }

    public function removeAccessRight(AccessRight $accessRight)
    {
        if ($this->getAccessRights()->contains($accessRight)) {
            $this->accessRights->removeElement($accessRight);
        }
        return $this;
    }

    /**
     * @param User[]|Collection $users
     *
     * @return UserGroup
     */
    public function setUsers($users)
    {
        foreach ($users as $user) {
            $user->setGroup($this);
        }

        $this->users = $users;

        return $this;
    }

    public function getUsers() {
        return $this->users;
    }

    public function addUser(User $user)
    {
        if (!$this->getUsers()->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }

    public function removeUser(User $user)
    {
        if ($this->getUsers()->contains($user)) {
            $this->users->removeElement($user);
        }
        return $this;
    }

    public function setDeleted(bool $delete) {
        $this->deleted = $delete;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return UserGroup
     */
    public function setName(string $name): UserGroup
    {
        $this->name = $name;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
    }

}
