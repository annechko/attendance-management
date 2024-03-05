<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Sort\CourseSort;
use App\Sort\SearchFilter;
use App\Sort\SortLoader;
use App\Form\CourseType;
use App\Form\SearchFilterForm;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/course')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'admin_course_index', methods: ['GET'])]
    public function index(
        CourseRepository $courseRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new CourseSort();
        $sortLoader->load($sort, $request);

        $errors = $validator->validate($sort);

        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_course_index');
        }

        return $this->render('admin/course/index.html.twig', [
            'search_form' => $form,
            'courses' => $courseRepository->buildSortedFilteredPaginatedList(
                $filter,
                $sort,
                $paginator,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_course_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            try {
                $entityManager->flush();
                return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $form->addError(new FormError('Internal error.'));
                $logger->error($exception->getMessage());
            }
        }

        return $this->render('admin/course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_course_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Course $course,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $form->addError(new FormError('Internal error.'));
                $logger->error($exception->getMessage());
            }
        }

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_course_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Course $course,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $course->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
