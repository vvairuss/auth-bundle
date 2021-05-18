<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Svyaznoy\Bundle\AuthBundle\Entity\AccessRight;
use Doctrine\Persistence\ManagerRegistry;

class AccessRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessRight::class);
    }

    public function findAttribute(string $attribute, ?string $subject)
    {
        return $this->findOneBy([
            'attribute' => $attribute,
            'subject' => $subject,
            'deleted' => 0,
        ]);
    }
}
