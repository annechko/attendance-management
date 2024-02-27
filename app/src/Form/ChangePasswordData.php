<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordData
{
    public string $oldPassword = '';

    #[Assert\Type('string')]
    #[Assert\Length(min: 6)]
    public string $newPassword = '';
}