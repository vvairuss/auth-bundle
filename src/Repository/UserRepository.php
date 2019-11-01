<?php

namespace Svyaznoy\Bundle\AuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     *
     * @return mixed|null|UserInterface
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $username = str_replace('@carbon.super', '', $username);

        return $this->createQueryBuilder('u')
            ->where('u.login = :login')
            ->andWhere('u.deleted = 0')
            ->setParameter('login', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
