<?php

namespace App\Controller\Admin;

use App\Entity\Period;
use App\Filter\PeriodSort;
use App\Filter\SearchFilter;
use App\Filter\SortLoader;
use App\Form\PeriodType;
use App\Form\SearchFilterForm;
use App\Repository\PeriodRepository;
use App\Repository\IntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/period')]
class PeriodController extends AbstractController
{
    #[Route('/', name: 'admin_period_index', methods: ['GET'])]
    public function index(
        PeriodRepository $periodRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new PeriodSort();
        $sortLoader->load($sort, $request);
        $errors = $validator->validate($sort);
        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_period_index');
        }
        return $this->render('admin/period/index.html.twig', [
            'search_form' => $form,
            'periods' => $periodRepository->buildSortedFilteredPaginatedList(
                $filter,
                $sort,
                $paginator,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_period_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        IntakeRepository $intakeRepository,
        EntityManagerInterface $entityManager,
        ): Response
    {
        $period = new Period();
        $intakeId = $request->get('intakeId');
        // Preselect intake if intakeId is provided
        if ($intakeId) {
            $intake = $intakeRepository->find($intakeId);
            if ($intake) {
                $period->setIntake($intake);
            }
        }

        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($period);
            $entityManager->flush();
            if ($intakeId) {
                return $this->redirectToRoute(
                    'admin_intake_show',
                    ['id' => $intakeId],
                    Response::HTTP_SEE_OTHER
            );
            } else {
                return $this->redirectToRoute('admin_period_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/period/new.html.twig', [
            'period' => $period,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_period_show', methods: ['GET'])]
    public function show(Period $period): Response
    {
        return $this->render('admin/period/show.html.twig', [
            'period' => $period,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_period_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Period $period,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_period_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/period/edit.html.twig', [
            'period' => $period,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_period_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Period $period,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $period->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($period);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_period_index', [], Response::HTTP_SEE_OTHER);
    }
}
