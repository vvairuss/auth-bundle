<?php

namespace Svyaznoy\Bundle\AuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AccessRight
 */
class AccessRight
{
    /**
     * @var int
     */
    private $id;

    /**
     * Название правила
     *
     * @var string
     */
    private $attribute;

    /**
     * Субьект правила
     *
     * @var string
     */
    private $subject;

    /**
     * Теги для группировки правил
     *
     * @var string
     */
    private $tags;

    /**
     * Описание правила
     *
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $deleted = 0;

    /**
     * @var ArrayCollection|UserGroup[]
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
