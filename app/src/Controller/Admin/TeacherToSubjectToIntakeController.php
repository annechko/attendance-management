<?php

namespace App\Controller\Admin;

use App\Entity\TeacherToSubjectToIntake;
use App\Form\TeacherToSubjectToIntakeType;
use App\Repository\TeacherRepository;
use App\Repository\TeacherToSubjectToIntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/teacher-to-subject-to-intake')]
class TeacherToSubjectToIntakeController extends AbstractController
{
    #[Route('/', name: 'admin_teacher_to_subject_to_intake_index', methods: ['GET'])]
    public function index(
        TeacherToSubjectToIntakeRepository $teacherToSubjectToIntakeRepository,
        TeacherRepository $teacherRepository,
        Request $request
    ): Response {
        $teacherId = (int) $request->get('teacherId', 0);
        $teacherName = null;

        if ($teacherId > 0) {
            $list = $teacherToSubjectToIntakeRepository->findAllByTeacherId($teacherId);
            $teacher = $teacherRepository->find($teacherId);
            $teacherName = $teacher?->getName();
        } else {
            $list = $teacherToSubjectToIntakeRepository->findAll();
        }

        return $this->render('admin/teacher_to_subject_to_intake/index.html.twig', [
            'teacher_to_subject_to_intakes' => $list,
            'teacherId' => $teacherId,
            'teacherName' => $teacherName,
        ]);
    }

    #[Route('/new', name: 'admin_teacher_to_subject_to_intake_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository
    ): Response {
        $teacherToSubjectToIntake = new TeacherToSubjectToIntake();
        $teacherId = (int) $request->get('teacherId', 0);

        $form = $this->createForm(
            TeacherToSubjectToIntakeType::class,
            $teacherToSubjectToIntake,
            ['teacherId' => $teacherId]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teacher = null;
            if ($teacherId > 0 && ($teacher = $teacherRepository->find($teacherId)) === null) {
                $form->addError(new FormError("Teacher with id = $teacherId not found."));
                return $this->render('admin/teacher_to_subject_to_intake/new.html.twig', [
                    'teacher_to_subject_to_intake' => $teacherToSubjectToIntake,
                    'form' => $form,
                ]);
            }
            if ($teacher) {
                $teacherToSubjectToIntake->setTeacher($teacher);
            }
            if (!$teacherToSubjectToIntake->isValid()) {
                $form->addError(
                    new FormError("Subject and intake should belong to the same course.")
                );
                return $this->render('admin/teacher_to_subject_to_intake/new.html.twig', [
                    'teacher_to_subject_to_intake' => $teacherToSubjectToIntake,
                    'form' => $form,
                ]);
            }
            $entityManager->persist($teacherToSubjectToIntake);
            $entityManager->flush();

            return $this->redirectToRoute(
                'admin_teacher_to_subject_to_intake_index',
                ['teacherId' => $teacherId],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/teacher_to_subject_to_intake/new.html.twig', [
            'teacher_to_subject_to_intake' => $teacherToSubjectToIntake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_teacher_to_subject_to_intake_show', methods: ['GET'])]
    public function show(TeacherToSubjectToIntake $teacherToSubjectToIntake): Response
    {
        return $this->render('admin/teacher_to_subject_to_intake/show.html.twig', [
            'teacher_to_subject_to_intake' => $teacherToSubjectToIntake,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_teacher_to_subject_to_intake_edit', methods: [
        'GET', 'POST',
    ])]
    public function edit(
        Request $request,
        TeacherToSubjectToIntake $teacherToSubjectToIntake,
        EntityManagerInterface $entityManager
    ): Response {
        $teacherId = (int) $request->get('teacherId', 0);
        $form = $this->createForm(
            TeacherToSubjectToIntakeType::class,
            $teacherToSubjectToIntake,
            ['teacherId' => $teacherId]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $params = [];
            if ($teacherId > 0) {
                $params['teacherId'] = $teacherId;
            }
            return $this->redirectToRoute(
                'admin_teacher_to_subject_to_intake_index',
                $params,
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/teacher_to_subject_to_intake/edit.html.twig', [
            'teacher_to_subject_to_intake' => $teacherToSubjectToIntake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_teacher_to_subject_to_intake_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        TeacherToSubjectToIntake $teacherToSubjectToIntake,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $teacherToSubjectToIntake->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($teacherToSubjectToIntake);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'admin_teacher_to_subject_to_intake_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
