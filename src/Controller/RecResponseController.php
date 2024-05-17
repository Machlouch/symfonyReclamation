<?php

namespace App\Controller;

use App\Entity\RecResponse;
use App\Form\RecResponseType;
use App\Repository\RecResponseRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\StarRep;
use App\Form\StarRepType;

#[Route('/rec/response')]
class RecResponseController extends AbstractController
{
   /* private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }*/
    #[Route('/{id}/rate', name: 'app_rec_response_rate', methods: ['POST'])]
    public function rate(Request $request, RecResponse $recResponse, EntityManagerInterface $entityManager): Response
    {
        $starRep = new StarRep();
        $starRep->setStarReps($recResponse); // Associate the rating with the RecResponse
        $starRep->setIdUser(1); 
        
        $form = $this->createForm(StarRepType::class, $starRep);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($starRep);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_rec_response_index');
        }
    
        // Render the form again or handle the form error.
        return $this->redirectToRoute('app_rec_response_index');
    }
    
    #[Route('/', name: 'app_rec_response_index', methods: ['GET'])]
    public function index(Request $request, RecResponseRepository $recResponseRepository, EntityManagerInterface $entityManager): Response
    {
        $query = $recResponseRepository->findAllQuery();
        $recResponses = $this->paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number, default is 1
            3 // Number of items per page
        );
    
        $forms = [];
        foreach ($recResponses as $recResponse) {
            $starRep = new StarRep();
            $starRep->setIdUser(1); // Assuming you want to set the user ID here
            $starRep->setStarReps($recResponse);
            $form = $this->createForm(StarRepType::class, $starRep, [
                'action' => $this->generateUrl('app_rec_response_rate', ['id' => $recResponse->getId()]),
            ]);
            $forms[$recResponse->getId()] = $form->createView();
        }
    
        return $this->render('rec_response/index.html.twig', [
            'rec_responses' => $recResponses,
            'forms' => $forms,
            
        ]);
    }
    

  
    

    #[Route('/{id}', name: 'app_rec_response_show', methods: ['GET'])]
    public function show(RecResponse $recResponse): Response
    {
        return $this->render('rec_response/show.html.twig', [
            'rec_response' => $recResponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rec_response_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecResponse $recResponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecResponseType::class, $recResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rec_response_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rec_response/edit.html.twig', [
            'rec_response' => $recResponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rec_response_delete', methods: ['POST'])]
    public function delete(Request $request, RecResponse $recResponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recResponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recResponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rec_response_index', [], Response::HTTP_SEE_OTHER);
    }
}
