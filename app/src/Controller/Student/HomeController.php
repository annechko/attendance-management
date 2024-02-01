<?php

declare(strict_types=1);

namespace App\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student')]
class HomeController extends AbstractController
{
    #[Route('', name: 'student_home')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig');
    }
}
