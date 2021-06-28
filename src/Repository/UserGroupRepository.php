<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Svyaznoy\Bundle\AuthBundle\Entity\UserGroup;
use Doctrine\Persistence\ManagerRegistry;

class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroup::class);
    }
}
