<?php

namespace App\Controller\Admin;

use App\Entity\PeriodToSubject;
use App\Form\PeriodToSubjectType;
use App\Repository\PeriodRepository;
use App\Repository\PeriodToSubjectRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/period-to-subject')]
class PeriodToSubjectController extends AbstractController
{
    #[Route('/', name: 'admin_period_to_subject_index', methods: ['GET'])]
    public function index(
        PeriodToSubjectRepository $periodToSubjectRepository,
        PeriodRepository $periodRepository,
        Request $request
    ): Response {
        $periodId = (int) $request->get('periodId', 0);
        $periodName = null;

        if ($periodId > 0) {
            $list = $periodToSubjectRepository->findAllByPeriodId($periodId);
            $period = $periodRepository->find($periodId);
            $periodName = $period?->getName();
        } else {
            $list = $periodToSubjectRepository->findAll();
        }

        return $this->render('admin/period_to_subject/index.html.twig', [
            'period_to_subjects' => $list,
            'periodId' => $periodId,
            'periodName' => $periodName,
        ]);
    }

    #[Route('/new', name: 'admin_period_to_subject_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PeriodRepository $periodRepository,
        LoggerInterface $logger
    ): Response {
        $periodToSubject = new PeriodToSubject();
        $periodId = (int) $request->get('periodId', 0);
        $courseId = null;
        if ($periodId > 0) {
            $period = $periodRepository->find($periodId);
            $courseId = $period?->getIntake()?->getCourse()?->getId();
        }

        $form = $this->createForm(
            PeriodToSubjectType::class,
            $periodToSubject,
            ['periodId' => $periodId, 'courseId' => $courseId]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: add validation - check same course for subject and period.
            $entityManager->persist($periodToSubject);
            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError('This subject is already related to this period.'));
                return $this->render('admin/period_to_subject/new.html.twig', [
                    'period_to_subject' => $periodToSubject,
                    'form' => $form,
                ]);
            } catch (\Throwable $exception) {
                $form->addError(new FormError('Sorry, there was an error.'));
                $logger->error($exception->getMessage());
                return $this->render('admin/period_to_subject/new.html.twig', [
                    'period_to_subject' => $periodToSubject,
                    'form' => $form,
                ]);
            }

            if ($periodId > 0) {
                return $this->redirectToRoute(
                    'admin_period_to_subject_index',
                    ['periodId' => $periodId],
                    Response::HTTP_SEE_OTHER
                );
            }
            return $this->redirectToRoute(
                'admin_period_to_subject_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/period_to_subject/new.html.twig', [
            'period_to_subject' => $periodToSubject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_period_to_subject_show', methods: ['GET'])]
    public function show(PeriodToSubject $periodToSubject): Response
    {
        return $this->render('admin/period_to_subject/show.html.twig', [
            'period_to_subject' => $periodToSubject,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_period_to_subject_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        PeriodToSubject $periodToSubject,
        EntityManagerInterface $entityManager
    ): Response {
        $periodId = (int) $request->get('periodId', 0);

        $form = $this->createForm(PeriodToSubjectType::class, $periodToSubject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            if ($periodId > 0) {
                return $this->redirectToRoute(
                    'admin_period_to_subject_index',
                    ['periodId' => $periodId],
                    Response::HTTP_SEE_OTHER
                );
            }
            return $this->redirectToRoute(
                'admin_period_to_subject_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/period_to_subject/edit.html.twig', [
            'period_to_subject' => $periodToSubject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_period_to_subject_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        PeriodToSubject $periodToSubject,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $periodToSubject->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($periodToSubject);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'admin_period_to_subject_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
