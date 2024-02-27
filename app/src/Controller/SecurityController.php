<?php

namespace App\Controller;

use App\Entity\PasswordChangebleInterface;
use App\Form\ChangePasswordData;
use App\Form\ChangePasswordDataForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            ['last_username' => $lastUsername, 'error' => $error]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }

    #[Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $data = new ChangePasswordData();
        $form = $this->createForm(ChangePasswordDataForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordChangebleInterface $user */
            $user = $this->getUser();
            try {
                $user->updatePassword(
                    $data->newPassword,
                    $userPasswordHasher
                );
                $entityManager->flush();
            } catch (\LogicException $exception) {
                $form->addError(new FormError($exception->getMessage()));
                return $this->render(
                    'security/change-password.html.twig',
                    [
                        'form' => $form,
                    ]
                );
            }

            return $this->redirectToRoute('app_logout');
        }

        return $this->render(
            'security/change-password.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
