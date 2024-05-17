<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\TypeReclamation;
use App\Form\TypeReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\TypeReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/type_reclamation')]
class TypeReclamationController extends AbstractController
{
   
    

    #[Route('/', name: 'app_type_reclamation_index', methods: ['GET'])]
    public function index(TypeReclamationRepository $typeReclamationRepository): Response
    {
        return $this->render('type_reclamation/index.html.twig', [
            'type_reclamations' => $typeReclamationRepository->findAll(),
        ]);
    }
    #[Route('/tri', name: 'app_type_reclamation_tri', methods: ['GET'])]
    public function tri(Request $request, TypeReclamationRepository $typeReclamationRepository): Response
    {
        // Retrieve sort field and direction from query parameters or set to default
        $sortField = $request->query->get('sort', 'nom');
        $sortDirection = $request->query->get('direction', 'ASC');
    
        // Ensure that the sort parameters are valid
        $validSortFields = ['nom']; // Add more fields as needed
        $validDirections = ['ASC', 'DESC'];
    
        // Check if the sort field and direction are valid
        if (!in_array($sortField, $validSortFields) || !in_array($sortDirection, $validDirections)) {
            // If not valid, use default values
            $sortField = 'nom';
            $sortDirection = 'ASC';
        }
    
        // Fetch the sorted data from the repository
        $typeReclamations = $typeReclamationRepository->findBy([], [$sortField => $sortDirection]);
    
        // Render the view with the sorted data
        return $this->render('type_reclamation/index.html.twig', [
            'type_reclamations' => $typeReclamations,
        ]);
    }
    
    #[Route('/recherche', name: 'app_type_reclamation_search')]
    public function searchByNom(Request $request, TypeReclamationRepository $typeReclamationRepository): Response
    {
        $nom = $request->query->get('nom');
        $typeReclamation = null;

        if ($nom) {
            $typeReclamation = $typeReclamationRepository->findOneByNom($nom);
        }

        return $this->render('type_reclamation/recherche.html.twig', [
            'typeReclamation' => $typeReclamation,
        ]);
    }


    #[Route('/statistiques/{nom}', name: 'app_type_reclamation_statistics', methods: ['GET'])]
public function statistics(TypeReclamationRepository $typeReclamationRepository): Response
{
    $percentages = $typeReclamationRepository->getPercentageByType();

    return $this->render('type_reclamation/statistics.html.twig', [
        'percentages' => $percentages,
    ]);
}

    

    
    #[Route('/new', name: 'app_type_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypeReclamationRepository $typeReclamationRepository): Response
    {
        $typeReclamation = new TypeReclamation();
        $form = $this->createForm(TypeReclamationType::class, $typeReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeReclamationRepository->save($typeReclamation, true);

            return $this->redirectToRoute('app_type_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_reclamation/new.html.twig', [
            'type_reclamation' => $typeReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_reclamation_show', methods: ['GET'])]
    public function show(TypeReclamation $typeReclamation): Response
    {
        return $this->render('type_reclamation/show.html.twig', [
            'type_reclamation' => $typeReclamation,
        ]);
    }
    #[Route('/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/{id}/edit', name: 'app_type_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeReclamation $typeReclamation, TypeReclamationRepository $typeReclamationRepository): Response
    {
        $form = $this->createForm(TypeReclamationType::class, $typeReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeReclamationRepository->save($typeReclamation, true);

            return $this->redirectToRoute('app_type_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_reclamation/edit.html.twig', [
            'type_reclamation' => $typeReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, TypeReclamation $typeReclamation, TypeReclamationRepository $typeReclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeReclamation->getId(), $request->request->get('_token'))) {
            $typeReclamationRepository->remove($typeReclamation, true);
        }

        return $this->redirectToRoute('app_type_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


}
