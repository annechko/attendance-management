<?php

namespace App\Controller\Admin;

use App\Entity\Institution;
use App\Filter\InstitutionFilter;
use App\Form\InstitutionFilterForm;
use App\Form\InstitutionType;
use App\Repository\InstitutionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/institution')]
class InstitutionController extends AbstractController
{
    private const MAX_PER_PAGE = 20;

    #[Route('/', name: 'admin_institution_index', methods: ['GET'])]
    public function index(
        InstitutionRepository $institutionRepository,
        PaginatorInterface $paginator,
        Request $request,
        ValidatorInterface $validator,
    ): Response {
        $filter = new InstitutionFilter();
        $form = $this->createForm(InstitutionFilterForm::class, $filter, [
            'method' => 'get',
        ]);
        $form->handleRequest($request);
        $filter->sort = $request->query->get('sort', 'id');
        $filter->direction = $request->query->get('direction', 'asc');
        $filter->page = (int) $request->query->get('page', 1);

        $errors = $validator->validate($filter);

        if (count($errors) > 0) {
            return $this->redirectToRoute('admin_institution_index');
        }

        return $this->render('admin/institution/index.html.twig', [
            'institutions' => $institutionRepository->buildSortedFilteredPaginatedList(
                $filter,
                $paginator,
                self::MAX_PER_PAGE,
            ),
        ]);
    }

    #[Route('/new', name: 'admin_institution_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $institution = new Institution();
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($institution);
            $entityManager->flush();

            return $this->redirectToRoute('admin_institution_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/institution/new.html.twig', [
            'institution' => $institution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_institution_show', methods: ['GET'])]
    public function show(Institution $institution): Response
    {
        return $this->render('admin/institution/show.html.twig', [
            'institution' => $institution,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_institution_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Institution $institution,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_institution_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/institution/edit.html.twig', [
            'institution' => $institution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_institution_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Institution $institution,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $institution->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($institution);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_institution_index', [], Response::HTTP_SEE_OTHER);
    }
}
