<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Security\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'admin_student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('admin/student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_student_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        EmailSender $emailSender,
    ): Response {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->setPassword(
                $userPasswordHasher->hashPassword(
                    $student,
                    $student->getFirstTimePassword()
                )
            );
            $entityManager->persist($student);
            $entityManager->flush();

            $emailSender->sendEmailOnRegistration($student, $student->getFirstTimePassword());

            return $this->redirectToRoute('admin_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('admin/student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_student_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Student $student,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();


            return $this->redirectToRoute('admin_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_student_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Student $student,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $student->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_student_index', [], Response::HTTP_SEE_OTHER);
    }
}
