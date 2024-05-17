<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Service;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Historique;
use App\Repository\HistoriqueRepository;

#[Route('/categorie')]
class CategorieController extends AbstractController
{  private $entityManager;
    private $historiqueRepository;
 


    public function __construct(EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository)
    {
        $this->entityManager = $entityManager;
        $this->historiqueRepository = $historiqueRepository;
    }

    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    #[Route('/recherche', name: 'app_categorie_search')]
    public function searchByName(Request $request, CategorieRepository $categorieRepository, HistoriqueRepository $historiqueRepository, EntityManagerInterface $entityManager): Response
    {
        $name = $request->query->get('name');
        $categorie = null;
    
        // Declare the description variable outside of the condition
        $description = "Recherche effectuée pour le nom : ";
    
        if ($name) {
            $categorie = $categorieRepository->findOneByName($name);
            // Update the description based on the search result
            $description .= $categorie ? "{$name}." : "{$name}, mais aucune catégorie trouvée.";
        } else {
            $description .= "Aucun nom fourni.";
        }
        
        // Create a new Historique entity and set its properties
        $historique = new Historique();
        $historique->setDescription($description);
        $historique->setCreatedAt(new \DateTime());
    
        // Persist the new Historique entity
        $entityManager->persist($historique);
        // Flush the changes to save them to the database
        $entityManager->flush();
    
        return $this->render('categorie/recherche.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    


    #[Route('/tri', name: 'app_categorie_tri', methods: ['GET'])]
    public function tri(Request $request, CategorieRepository $categorieRepository, HistoriqueRepository $historiqueRepository, EntityManagerInterface $entityManager): Response
    {
        $sortField = $request->query->get('sort', 'name'); // 'name' est le tri par défaut
        $sortDirection = $request->query->get('direction', 'ASC'); // 'ASC' est la direction par défaut
    
        // Assurez-vous que les paramètres de tri sont valides
        $validSortFields = ['name']; // Ajoutez tous les champs valides de l'entité Catégorie ici
        $validDirections = ['ASC', 'DESC'];
    
        if (!in_array($sortField, $validSortFields) || !in_array($sortDirection, $validDirections)) {
            throw new \InvalidArgumentException('Invalid sort field or direction');
        }
    
        $categories = $categorieRepository->findBySortedField($sortField, $sortDirection);
    
        // Create a new Historique entity and set its properties
        $historique = new Historique();
        $historique->setDescription("Tri effectué sur le champ '{$sortField}' dans l'ordre '{$sortDirection}'.");
        $historique->setCreatedAt(new \DateTime());
    
        // Persist the new Historique entity
        $entityManager->persist($historique);
        // Flush the changes to save them to the database
        $entityManager->flush();
    
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    
 
    #[Route('/add', name: 'categorie_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
    
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            // Log the creation of a new category
            $historique = new Historique();
            $historique->setDescription("Created a new category: " . $categorie->getName());
            $historique->setCreatedAt(new \DateTime());
            $this->entityManager->persist($historique);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository): Response
    {
        $originalData = clone $categorie; // Clone the original data before changes
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->flush();
    
            // Create a historique entry after a successful edit
            $historique = new Historique();
            $changeDescription = "Category '{$originalData->getName()}' was edited.";
            $historique->setDescription($changeDescription);
            $historique->setCreatedAt(new \DateTime());
            
            // Persist the historique entry
            $entityManager->persist($historique);
            $entityManager->flush(); // Flush again to save the historique
    
            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(), // Use createView() when passing forms to Twig
        ]);
    }
    
    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]

    public function show(Categorie $Categorie) : Response
    {
        return $this->render('Categorie/show.html.twig', [
            'categorie' => $Categorie,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            // Log the deletion in historique before actually removing the category
            $historique = new Historique();
            $historique->setDescription("Category deleted: " . $categorie->getName());
            $historique->setCreatedAt(new \DateTime());
            $entityManager->persist($historique);
    
            // Now remove the category
            $entityManager->remove($categorie);
            $entityManager->flush(); // This flush will commit both the remove and the persist operations
        }
    
        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
  
    
    #[Route('/historique', name: 'app_categorie_historique')]
public function historique(Categorie $categorieRepository, HistoriqueRepository $historiqueRepository): Response
{
    // Assuming you have a method in your repository to fetch the historique
    $historiques = $historiqueRepository->findHistoriquesForCategories();

    return $this->render('historique/index.html.twig', [
        'historiques' => $historiques,
    ]);
}

}