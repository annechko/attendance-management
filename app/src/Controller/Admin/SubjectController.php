<?php

namespace App\Controller\Admin;

use App\Entity\Subject;
use App\Form\SearchFilterForm;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use App\Sort\SearchFilter;
use App\Sort\SortLoader;
use App\Sort\SubjectSort;
use App\Sort\TeacherSort;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'admin_subject_index', methods: ['GET'])]
    public function index(
        SubjectRepository $subjectRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new SubjectSort();
        $sortLoader->load($sort, $request);
        $errors = $validator->validate($sort);
        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_subject_index');
        }
        return $this->render('admin/subject/index.html.twig', [
            'search_form' => $form,
            'subjects' => $subjectRepository->buildSortedFilteredPaginatedList(
                $filter,
                $sort,
                $paginator,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('admin_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/subject/new.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_subject_show', methods: ['GET'])]
    public function show(Subject $subject): Response
    {
        return $this->render('admin/subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_subject_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Subject $subject,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/subject/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_subject_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Subject $subject,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $subject->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($subject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_subject_index', [], Response::HTTP_SEE_OTHER);
    }
}
