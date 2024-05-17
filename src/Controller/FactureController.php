<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Commande;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/Facture")
 */
class FactureController extends AbstractController
{
    /**
     * @Route("/", name="Facture_index", methods={"GET"})
     */
    public function index(FactureRepository $FactureRepository): Response
    {$FactureQuery = $FactureRepository->createQueryBuilder('r')
        ->orderBy('r.date', 'DESC')
        ->getQuery();

    
        
        
        return $this->render('Facture/index.html.twig', [
            'Factures' => $FactureRepository->findAll(),
        ]);

    }
      /**
     * @Route("/{id}/edit", name="Facture_edit", methods={"GET","POST"})
     */
    public function edit2(Request $request, Facture $Facture): Response
    {
        $form = $this->createForm(FactureType::class, $Facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Facture_index');
        }

        return $this->render('Facture/edit.html.twig', [
            'Facture' => $Facture,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/new/{id}", name="Facture_new", methods={"GET","POST"})
     */
    public function new(Request $request,$id,FactureRepository $FactureRepository): Response
    {
        $Facture = new Facture();
        $entityManager = $this->getDoctrine()->getManager();
        $Commande = $entityManager->getRepository(Commande::class)->find($id);
        
       
        $form = $this->createForm(FactureType::class, $Facture);
        $form->handleRequest($request);

     
            $Facture->setCommande($Commande);
            
            $Facture->setNom($Commande->getNom());
            
            $Facture->setPrenom($Commande->getPrenom());
            
            $Facture->setEmail($Commande->getEmail());
            
            $Facture->setAdresse($Commande->getAdresse());
            $Facture->setTotalcost($Commande->getTotalcost()); 

            $Facture->setNumtelephone($Commande->getNumtelephone()); 

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Facture);
            $entityManager->flush();
            

            return $this->redirectToRoute('Facture_index');
      
     
        
    }

 /**
     * @Route("/{Factureid}", name="Facture_delete", methods={"POST"})
     */
    public function delete(Request $request, Facture $Facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Facture->getFactureid(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Facture_index');
    }
   /**
 * @Route("/list/{id}", name="facture_list", methods={"GET"})
 */
public function listl(FactureRepository $FactureRepository, int $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFront', 'Arial');

    $dompdf = new Dompdf($pdfOptions);
    
    // Fetch the specific Facture based on the ID
    $Facture = $entityManager->getRepository(Facture::class)->find($id);

    // Check if Facture exists
    if (!$Facture) {
        throw $this->createNotFoundException('Facture not found');
    }

    $html = $this->renderView('Facture/list.html.twig', [
        'factures' => [$Facture], // Pass the Facture as an array
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $response = new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
    ]);

    // Set the response headers for downloading the file
    $response->headers->set('Content-Disposition', 'inline; filename="mypdf.pdf"');

    return $response;
}

}
