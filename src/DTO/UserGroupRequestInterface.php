<?php

namespace Svyaznoy\Bundle\AuthBundle\DTO;

interface UserGroupRequestInterface
{
    public function getName(): string;
    public function getUserIds(): ?array;
    public function getAccessRightIds(): array;
}
