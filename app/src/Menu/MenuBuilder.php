<?php

namespace App\Menu;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Security\RouteHelper;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;

class MenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly RouteHelper $routeHelper,
        private readonly Security $security,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav flex-column');

        $menu->addChild('Home', [
            'route' => $this->routeHelper->generateHomeForCurrentUser(),
            'extras' => ['icon' => 'home'],
        ]);

        match (get_class($this->security->getUser())) {
            Admin::class => $this->addAdminMenuItems($menu),
            Student::class => $this->addStudentMenuItems($menu),
            Teacher::class => $this->addTeacherMenuItems($menu),
        };

        // common style for all menu items.
        foreach ($menu->getChildren() as $item) {
            $item->setLinkAttributes(['class' => 'nav-link']);
            $item->setAttributes(['class' => 'nav-item']);
        }

        return $menu;
    }

    private function addAdminMenuItems(ItemInterface $menu): void
    {
        $menu->addChild('Institutions', [
            'route' => 'admin_institution_index',
            'extras' => ['icon' => 'package'],
        ]);
        $menu->addChild('Courses', [
            'route' => 'admin_course_index',
            'extras' => ['icon' => 'sidebar'],
        ]);
        $menu->addChild('Intakes', [
            'route' => 'admin_intake_index',
            'extras' => ['icon' => 'calendar'],
        ]);
        $menu->addChild('Periods', [
            'route' => 'admin_period_index',
            'extras' => ['icon' => 'columns'],
        ]);
        $menu->addChild('Subjects', [
            'route' => 'admin_subject_index',
            'extras' => ['icon' => 'file-text'],
        ]);
        $menu->addChild('Students', [
            'route' => 'admin_student_index',
            'extras' => ['icon' => 'users'],
        ]);
        $menu->addChild('Teachers', [
            'route' => 'admin_teacher_index',
            'extras' => ['icon' => 'book'],
        ]);
    }

    private function addStudentMenuItems(ItemInterface $menu): void
    {

    }

    private function addTeacherMenuItems(ItemInterface $menu): void
    {
        $menu->addChild('Subjects', [
            'route' => 'teacher_to_subject_to_intake_index',
            'extras' => ['icon' => 'book'],
        ]);
        $menu->addChild('Attendance', [
            'route' => 'teacher_attendance_index',
            'extras' => ['icon' => 'book'],
        ]);
    }
}