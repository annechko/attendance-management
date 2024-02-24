<?php

namespace App\Controller\Admin;

use App\Entity\Intake;
use App\Filter\IntakeSort;
use App\Filter\SearchFilter;
use App\Filter\SortLoader;
use App\Form\IntakeType;
use App\Form\SearchFilterForm;
use App\Repository\IntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/intake')]
class IntakeController extends AbstractController
{
    #[Route('/', name: 'admin_intake_index', methods: ['GET'])]
    public function index(
        IntakeRepository $intakeRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new IntakeSort();
        $sortLoader->load($sort, $request);
        $errors = $validator->validate($sort);
        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_intake_index');
        }
        return $this->render('admin/intake/index.html.twig', [
            'search_form' => $form,
            'intakes' => $intakeRepository->buildSortedFilteredPaginatedList(
                $filter,
                $sort,
                $paginator,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_intake_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $intake = new Intake();
        $form = $this->createForm(IntakeType::class, $intake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($intake);
            $entityManager->flush();

            return $this->redirectToRoute('admin_intake_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/intake/new.html.twig', [
            'intake' => $intake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_intake_show', methods: ['GET'])]
    public function show(Intake $intake): Response
    {
        return $this->render('admin/intake/show.html.twig', [
            'intake' => $intake,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_intake_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Intake $intake,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(IntakeType::class, $intake);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_intake_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/intake/edit.html.twig', [
            'intake' => $intake,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_intake_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Intake $intake,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $intake->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($intake);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_intake_index', [], Response::HTTP_SEE_OTHER);
    }
}
