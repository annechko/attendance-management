<?php

namespace App\Controller\Teacher;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractTeacherController extends AbstractController
{
    public function __construct(private readonly TeacherRepository $teacherRepository)
    {
    }

    protected function getCurrentTeacher(): Teacher
    {
        return $this->teacherRepository->findOneBy(
            ['email' => $this->getUser()->getUserIdentifier()]
        );
    }
}
