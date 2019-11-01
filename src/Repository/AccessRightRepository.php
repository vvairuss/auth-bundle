<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Svyaznoy\Bundle\AuthBundle\Entity\AccessRight;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AccessRightRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
