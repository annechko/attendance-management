<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\RouteHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(RouteHelper $routeHelper): Response
    {
        $target = $routeHelper->generateHomeForUser($this->getUser());

        return $this->redirectToRoute($target);
    }
}
