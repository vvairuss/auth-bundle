<?php

namespace Svyaznoy\Bundle\AuthBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Svyaznoy\Bundle\AuthBundle\DTO\UserGroupRequestInterface;
use Svyaznoy\Bundle\AuthBundle\Entity\AccessRight;
use Svyaznoy\Bundle\AuthBundle\Entity\User;
use Svyaznoy\Bundle\AuthBundle\Entity\UserGroup;
use Svyaznoy\Bundle\AuthBundle\Repository\AccessRightRepository;
use Svyaznoy\Bundle\AuthBundle\Repository\UserGroupRepository;
use Svyaznoy\Bundle\AuthBundle\Repository\UserRepository;


class UserGroupService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var AccessRightRepository
     */
    protected $accessRightRepository;

    /**
     * @var AccessRightRepository
     */
    protected $userGroupRepository;

    /**
     * UserGroupService constructor.
     * @param UserGroupRepository $userGroupRepository
     * @param UserRepository $userRepository
     * @param AccessRightRepository $accessRightRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserGroupRepository $userGroupRepository,
        UserRepository $userRepository,
        AccessRightRepository $accessRightRepository,
        EntityManagerInterface $em
    ) {
        $this->userGroupRepository = $em->getRepository(UserGroup::class);
        $this->userRepository = $userRepository;
        $this->accessRightRepository = $accessRightRepository;
//        $this->accessRightRepository = $em->getRepository(AccessRight::class);
        $this->em = $em;
    }

    /**
     * Создание группы пользователей
     *
     * @param UserGroupRequestInterface $userGroupRequest
     *
     * @return UserGroup
     */
    public function create(UserGroupRequestInterface $userGroupRequest): UserGroup
    {
        $userCollection = new ArrayCollection($this->userRepository->findBy(['id' => $userGroupRequest->getUserIds()]));
        $rightCollection = new ArrayCollection(
            $this->accessRightRepository->findBy(['id' => $userGroupRequest->getAccessRightIds()])
        );

        $newUserGroup = new UserGroup($userGroupRequest->getName(), $userCollection, $rightCollection);

        $this->em->persist($newUserGroup);
        $this->em->flush();

        return $newUserGroup;
    }

    /**
     * @param UserGroup $userGroup
     * @param UserGroupRequestInterface $userGroupRequest
     *
     * @return UserGroup
     */
    public function edit(UserGroup $userGroup, UserGroupRequestInterface $userGroupRequest): UserGroup
    {
        $rightCollection = new ArrayCollection(
            $this->accessRightRepository->findBy(['id' => $userGroupRequest->getAccessRightIds()])
        );

        /** @var UserGroup $userGroup */
        $userGroup->setName($userGroupRequest->getName());
        $userGroup->setAccessRights($rightCollection);

        $this->em->persist($userGroup);
        $this->em->flush();

        return $userGroup;
    }

    /**
     * Клонирование группы
     *
     * @param UserGroup $userGroup
     *
     * @return UserGroup
     */
    public function clone(UserGroup $userGroup): UserGroup
    {
        $newUserGroup = clone $userGroup;
        $newUserGroup->setUsers(new ArrayCollection());
        $this->em->detach($newUserGroup);
        $this->em->persist($newUserGroup);
        $this->em->flush();

        return $newUserGroup;
    }

    /**
     * Удаление группы
     *
     * @param UserGroup $userGroup
     */
    public function delete(UserGroup $userGroup): void
    {
        // если в группе есть пользователи то сперва надо их перенести в другую группу
        if ($userGroup->getUsers()->count() > 0) {
            throw new \InvalidArgumentException('Group not empty');
        }
        $userGroup->setDeleted(true);
        $this->em->persist($userGroup);
        $this->em->flush();
    }

    public function addUserToGroup(UserGroup $userGroup, User $user): UserGroup
    {
        $user->setGroup($userGroup);
        $userGroup->addUser($user);
        $this->em->persist($user);
        $this->em->persist($userGroup);
        $this->em->flush();

        return $userGroup;
    }

    public function addRule(UserGroup $userGroup, AccessRight $accessRight): UserGroup
    {
        $userGroup->addAccessRights($accessRight);
        $this->em->persist($userGroup);
        $this->em->flush();

        return $userGroup;
    }

    public function removeRule(UserGroup $userGroup, AccessRight $accessRight): UserGroup
    {
        $userGroup->removeAccessRight($accessRight);
        $this->em->persist($userGroup);
        $this->em->flush();

        return $userGroup;
    }

    /**
     * @return AccessRight[]|array|null
     * @todo: прокси метод. Перенести в репозиторий accessRight
     */
    public function getAllRights()
    {
        return $this->accessRightRepository->findBy(['deleted' => 0]);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function setUserGroupByRole(User $user): void
    {
        $userGroup = $this->converterRoleToGroup($user);
        $user->setGroup($userGroup);
    }

    private function converterRoleToGroup(User $user): UserGroup
    {
        if (null !== $user->getGroup()) {
            /** нечего конвертировать, если группа уже выставлена */
            return $user->getGroup();
        }
        switch ($user->getRole()) {
            case 1:
                $group = 1;
                break;
            case 2:
                $group = 2;
                break;
            case 4:
                $group = 3;
                break;
            case 8:
                $group = 4;
                break;
            case 16:
                $group = 5;
                break;
            case 32:
                $group = 6;
                break;
            case 64:
                $group = 7;
                break;
            case 128:
                $group = 8;
                break;
            case 256:
                $group = 9;
                break;
            default:
                throw new \Exception('Не известная роль для конвертации');
        }

        return $this->userGroupRepository->find($group);
    }

    /**
     * Метод проверки прав пользователя
     * Делает то же самое, что и вотеры, но только для определенного, а не для текущего пользователя
     *
     * @param User $user
     * @param $attribute
     * @param null $subject
     * @return bool
     */
    public function checkUserRight(User $user, $attribute, $subject = null) :bool
    {
        $group = $user->getGroup();

        if (!$group instanceof UserGroup) {
            return false;
        }

        $isAccessRightExists = $group->getAccessRights()->exists(function($key, $accessRight) use ($attribute, $subject) {
            /** @var AccessRight $accessRight */
            return $accessRight->getAttribute() === $attribute && $accessRight->getSubject() === $subject;
        });

        return  $isAccessRightExists;
    }
}
