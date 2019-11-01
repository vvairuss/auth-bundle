<?php

namespace Svyaznoy\Bundle\AuthBundle\Security;

use Svyaznoy\Bundle\AuthBundle\Entity\AccessRight;
use Svyaznoy\Bundle\AuthBundle\Entity\UserGroup;
use Svyaznoy\Bundle\AuthBundle\Repository\AccessRightRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessRightGroupVoter extends Voter
{
    private $accessRightRepository;

    /**
     * @param AccessRightRepository $accessRightRepository
     */
    public function __construct(AccessRightRepository $accessRightRepository)
    {
        $this->accessRightRepository = $accessRightRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (is_object($subject) && !method_exists($subject, "__toString")) {
            return false;
        }

        return (bool)$this->accessRightRepository->findAttribute($attribute, $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $group = $token->getUser()->getGroup();

        if (!$group instanceof UserGroup) {
            return false;
        }

        $isAccessRightExists = $group->getAccessRights()->exists(
            static function($key, $accessRight) use ($attribute, $subject) {
            /** @var AccessRight $accessRight */
            return $accessRight->getAttribute() === $attribute && $accessRight->getSubject() === $subject;
        });

        return $isAccessRightExists;
    }
}
