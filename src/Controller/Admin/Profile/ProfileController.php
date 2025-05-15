<?php

namespace App\Controller\Admin\Profile;

use App\Entity\User;
use App\Form\EditProfileFormType;
use App\Form\EditProfilePasswordFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
final class ProfileController extends AbstractController
{
    #[Route('/profile/index', name: 'app_admin_profile_index', methods: ['GET'])]
    public function index(): Response
    {
        // Récupérons l'utilisateur connecté
        // Parce que l'utilisateur qui demande à voir son profil, c'est utilisateur connecté.
        /** @var User */
        $admin = $this->getUser();

        return $this->render('pages/admin/profile/index.html.twig', [
            "admin" => $admin
        ]);
    }


    #[Route('/profile/edit', name: 'app_admin_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User */
        $admin = $this->getUser();

        $form = $this->createForm(EditProfileFormType::class, $admin);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $admin->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', "Le profil est modifié");

            return $this->redirectToRoute('app_admin_profile_index');
        }

        return $this->render('pages/admin/profile/edit_profile.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/profile/edit/password', name: 'app_admin_profile_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(
        Request $request, 
        UserPasswordHasherInterface $hasher, 
        EntityManagerInterface $entityManager
    ): Response 
    {

        /** @var User */
        $admin = $this->getUser();

        $form = $this->createForm(EditProfilePasswordFormType::class, null);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {

            // $data = $request->request->all();
            // dd($data['edit_profile_password_form']['plainPassword']['first']);

            $data = $form->getData();
            $newPlainPassword = $data['plainPassword'];

            $passwordHashed = $hasher->hashPassword($admin, $newPlainPassword);
            $admin->setPassword($passwordHashed);
            $admin->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', "Votre mot de passe a été modifié.");

            return $this->redirectToRoute('app_admin_profile_index');
        }

        return $this->render('pages/admin/profile/edit_password.html.twig', [
            "form" => $form->createView()
        ]);
    }


}
