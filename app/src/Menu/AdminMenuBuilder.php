<?php

namespace App\Menu;

use App\Security\RouteHelper;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class AdminMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly RouteHelper $routeHelper,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav flex-column');


        $menu->addChild('Home', [
            'route' => $this->routeHelper->generateHomeForCurrentUser(),
            'attributes' => ['class' => 'nav-item'],
            'linkAttributes' => ['class' => 'nav-link'],
            'extras' => ['icon' => 'home'],
        ]);
        $menu->addChild('Students', [
            'route' => 'admin_student_index',
            'attributes' => ['class' => 'nav-item'],
            'linkAttributes' => ['class' => 'nav-link'],
            'extras' => ['icon' => 'users'],
        ]);

        return $menu;
    }
}