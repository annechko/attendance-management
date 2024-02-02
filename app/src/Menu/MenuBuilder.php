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
        $menu->addChild('Students', [
            'route' => 'admin_student_index',
            'extras' => ['icon' => 'users'],
        ]);
    }

    private function addStudentMenuItems(ItemInterface $menu): void
    {

    }

    private function addTeacherMenuItems(ItemInterface $menu): void
    {

    }
}