<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/admin/user/{id}/add-role-editor', name: 'app_user_role_editor')]
    public function changeRole(User $user, EntityManagerInterface $entityManagerInterface): Response
    {
        $user->setRoles(["ROLE_EDITOR", "ROLE_USER"]);
        $entityManagerInterface->flush();
        $this->addFlash('success', 'Edited');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove-role-editor', name: 'app_user_remove_role_editor')]
    public function removeRole(User $user, EntityManagerInterface $entityManagerInterface): Response
    {
        $user->setRoles([ "ROLE_USER"]);
        $entityManagerInterface->flush();
        $this->addFlash('danger', 'Role Removed');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove', name: 'app_user_remove')]
    public function remove(User $user, EntityManagerInterface $entityManagerInterface, UserRepository $userRepository): Response
    {
        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();
        $this->addFlash('danger', 'Removed');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/edit', name: 'app_user_edit')]
    public function edit(User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
       
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/edit.html.twig', [
            'registrationForm' => $form,
            'user'=>$user
        ]);
    }
}
