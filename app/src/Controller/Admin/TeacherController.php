<?php

namespace App\Controller\Admin;

use App\Entity\Teacher;
use App\Form\SearchFilterForm;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use App\Security\EmailSender;
use App\Sort\SearchFilter;
use App\Sort\SortLoader;
use App\Sort\TeacherSort;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/teacher')]
class TeacherController extends AbstractController
{
    #[Route('/', name: 'admin_teacher_index', methods: ['GET'])]
    public function index(
        TeacherRepository $teacherRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new TeacherSort();
        $sortLoader->load($sort, $request);
        $errors = $validator->validate($sort);
        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_teacher_index');
        }
        return $this->render('admin/teacher/index.html.twig', [
            'search_form' => $form,
            'teachers' => $teacherRepository->buildSortedFilteredPaginatedList(
                $filter,
                $sort,
                $paginator,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_teacher_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        EmailSender $emailSender,
    ): Response {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teacher->setPassword(
                $userPasswordHasher->hashPassword(
                    $teacher,
                    $teacher->getFirstTimePassword()
                )
            );
            $entityManager->persist($teacher);
            $entityManager->flush();

            $emailSender->sendEmailOnRegistration($teacher, $teacher->getFirstTimePassword());

            return $this->redirectToRoute('admin_teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/teacher/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        return $this->render('admin/teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_teacher_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Teacher $teacher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/teacher/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_teacher_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Teacher $teacher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $teacher->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($teacher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_teacher_index', [], Response::HTTP_SEE_OTHER);
    }
}
