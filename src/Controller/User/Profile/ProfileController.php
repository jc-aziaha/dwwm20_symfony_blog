<?php

namespace App\Controller\User\Profile;

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

#[Route('/user')]
final class ProfileController extends AbstractController
{
    #[Route('/profile/index', name: 'app_user_profile_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('pages/user/profile/index.html.twig', [
            "user" => $user
        ]);
    }


    #[Route('/profile/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {

        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(EditProfileFormType::class, $user);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $user->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Votre profil est modifié.");

            return $this->redirectToRoute('app_user_profile_index');
        }

        return $this->render('pages/user/profile/edit_profile.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/profile/edit/password', name: 'app_user_profile_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(
        Request $request, 
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager
    ): Response
    {

        $form = $this->createForm(EditProfilePasswordFormType::class, null);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $plainPassword = $form->getData()['plainPassword'];
            
            /** @var User */
            $user = $this->getUser();
            
            $passwordHashed = $hasher->hashPassword($user, $plainPassword);

            $user->setPassword($passwordHashed);
            $user->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Le mot de passe a été modifié");

            return $this->redirectToRoute('app_user_profile_index');
        }

        return $this->render('pages/user/profile/edit_password.html.twig', [
            "form" => $form->createView()
        ]);
    }


}
