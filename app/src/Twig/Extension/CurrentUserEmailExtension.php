<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentUserEmailExtension extends AbstractExtension
{
    public function __construct(private readonly Security $security,)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_email', $this->getEmail(...)),
        ];
    }

    public function getEmail(): string
    {
        return $this->security->getUser()?->getUserIdentifier() ?? '';
    }
}
