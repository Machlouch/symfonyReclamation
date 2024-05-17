<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UtilisateurRepository;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UtilisateurRepository $utilisateurRepository): Response
    {
        $user = $this->getUser();
        if ($this->getUser()) {
            if ($user->getRole() == "Admin") {
                return $this->redirectToRoute('app_dashboard');
            }
            if (!$user->getIsVerified()) {
    
                // Logout the user
                $this->get('security.token_storage')->setToken(null);
                $this->get('session')->invalidate();
    
                // Redirect to the login page
                $this->addFlash('notverified', 'Your account is not yet verified. Please check your email for verification instructions ');

                return $this->redirectToRoute('app_login');
            }
               

            return $this->redirectToRoute('app_home');
            
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    //crearte function goHome that redirect to home page 
    #[Route(path: '/home', name: 'app_home')]
    public function goHome(UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $utilisateurRepository->findOneByEmail($this->getUser()->getUserIdentifier());

        if ($user->getRole() == "Admin") {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('security/home.html.twig', [
            'aaa' => $user,
        ]);
    }
    #[Route(path: '/dashboard', name: 'app_dashboard')]
    public function goDashboard(UtilisateurRepository $utilisateurRepository): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $utilisateurRepository->findOneByEmail($this->getUser()->getUserIdentifier());
        if ($user->getRole() != "Admin") {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/dashboard.html.twig', [
            'aaa' => $user,
        ]);
    }
}
