<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\AdminEditRolesFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
final class UserController extends AbstractController
{
    #[Route('/user/index', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('pages/admin/user/index.html.twig', [
            "users" => $users
        ]);
    }


    #[Route('/user/edit/{id<\d+>}', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editRoles(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AdminEditRolesFormType::class, $user);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $user->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Les rôles de {$user->getFirstName()} {$user->getLastName()} ont été modifiés avec succès.");

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('pages/admin/user/edit_roles.html.twig', [
            "form" => $form
        ]);
    }

    #[Route('/user/delete/{id<\d+>}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ( $this->isCsrfTokenValid("delete_user_{$user->getId()}", $request->request->get('csrf_token')) ) 
        {

            // Comptons le nombre total d'articles rédigé par l'utilisateur à supprimer.
            // S'il a rédigé au moins un article,
            if( count($user->getPosts()) > 0 )
            {
                // Pour chaque article de la liste,
                foreach ($user->getPosts() as $post) 
                {
                    // Le détacher de l'utilisateur correspondant
                    $post->setUser(null);
                }
            }

            // Si l'utilisateur connecté est le même que celui à supprimer
            if ( $user === $this->getUser() )
            {
                // Supprimons le jéton de sécurité correspondant de l'utilisateur connecté.
                $this->container->get('security.token_storage')->setToken(null);
            }

            // Supprimer l'utilisateur de la base de données
            $entityManager->remove($user);
            $entityManager->flush();

            // Générer le message flash de succès,
            $this->addFlash('success', "{$user->getFirstName()} {$user->getLastName()} a été supprimé.");
        }

        // Effectuer une redirection vers la page listant les utilisateurs.
        return $this->redirectToRoute('app_admin_user_index');
    }


}
