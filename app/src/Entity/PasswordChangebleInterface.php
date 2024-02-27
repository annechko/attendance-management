<?php

namespace App\Entity;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

interface PasswordChangebleInterface
{
    public function updatePassword(
        string $new,
        UserPasswordHasherInterface $userPasswordHasher
    ): void;
}