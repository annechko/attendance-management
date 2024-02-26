<?php

namespace App\Controller\Student;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractStudentController extends AbstractController
{
    public function __construct(private readonly StudentRepository $studentRepository)
    {
    }

    protected function getCurrentStudent(): Student
    {
        return $this->studentRepository->findOneBy(
            ['email' => $this->getUser()->getUserIdentifier()]
        );
    }
}
