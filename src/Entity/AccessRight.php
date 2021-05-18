<?php

namespace Svyaznoy\Bundle\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AccessRight
 *
 * @ORM\Table(name="access_right")
 * @ORM\Entity(repositoryClass="Svyaznoy\Bundle\AuthBundle\Repository\AccessRightRepository")
 */
class AccessRight
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
     * Название правила
     *
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=false)
     */
    private $attribute;

    /**
     * Субьект правила
     *
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $subject;

    /**
     * Теги для группировки правил
     *
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $tags;

    /**
     * Описание правила
     *
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean", nullable=false, options={"unsigned":true})
     */
    private $deleted = 0;

    /**
     * @var ArrayCollection|UserGroup[]
     *
     * @ORM\ManyToMany(targetEntity="Svyaznoy\Bundle\AuthBundle\Entity\UserGroup")
     * @ORM\JoinTable(name="access_right_groups",
     *      joinColumns={@ORM\JoinColumn(name="access_right_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setGroups(Collection $groups): AccessRight
    {
        $this->groups = $groups;

        return $this;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getTags(): string
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function addGroups(UserGroup $userGroup): AccessRight
    {
        if (!$this->getGroups()->contains($userGroup)) {
            $this->groups->add($userGroup);
        }

        return $this;
    }

    public function removeGroups(UserGroup $userGroup): AccessRight
    {
        if ($this->getGroups()->contains($userGroup)) {
            $this->groups->removeElement($userGroup);
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
