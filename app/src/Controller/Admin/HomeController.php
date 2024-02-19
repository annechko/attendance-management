<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdminRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\InstitutionRepository;
use App\Repository\CourseRepository;
use App\Repository\SubjectRepository;
use App\Repository\IntakeRepository;
use App\Repository\PeriodRepository;


class HomeController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(
        AdminRepository $adminrepo,
        StudentRepository $studentrepo,
        TeacherRepository $teacherrepo,
        InstitutionRepository $institutionrepo,
        CourseRepository $coursesrepo,
        SubjectRepository $subjectsrepo,
        IntakeRepository $intakesrepo,
        PeriodRepository $periodsrepo,
        ): Response
    {
        return $this->render('admin/index.html.twig', [
            'administrators' => $adminrepo->count([]),
            'students' => $studentrepo->count([]),
            'teachers' => $teacherrepo->count([]),
            'institutions' => $institutionrepo->count([]),
            'courses' => $coursesrepo->count([]),
            'subjects' => $subjectsrepo->count([]),
            'intakes' => $intakesrepo->count([]),
            'periods' => $periodsrepo->count([]),
        ]);
    }
}
