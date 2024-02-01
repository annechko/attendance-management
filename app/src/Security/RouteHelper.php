<?php

namespace App\Security;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class RouteHelper
{
    private ?string $currentUserClassName;

    private const ROUTE_DEFAULT = 'app_login';
    private const USER_CLASS_TO_HOME_ROUTE = [
        Admin::class => 'admin_home',
        Student::class => 'student_home',
        Teacher::class => 'teacher_home',
    ];

    public function __construct(
        readonly Security $security,
    ) {
        $this->currentUserClassName = $security->getUser()
            ? get_class($security->getUser())
            : null;
    }

    public function generateHomeForCurrentUser(): string
    {
        return self::USER_CLASS_TO_HOME_ROUTE[$this->currentUserClassName] ?? self::ROUTE_DEFAULT;
    }

    public function generateHomeForUser(?UserInterface $user): string
    {
        if ($user === null) {
            return self::ROUTE_DEFAULT;
        }
        return self::USER_CLASS_TO_HOME_ROUTE[get_class($user)] ?? self::ROUTE_DEFAULT;
    }
}