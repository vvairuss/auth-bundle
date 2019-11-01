<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Svyaznoy\Bundle\AuthBundle\Entity\UserGroup;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserGroup::class);
    }
}
