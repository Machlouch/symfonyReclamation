<?php

namespace App\Controller;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Historique;
use App\Entity\Rating;
use App\Form\RatingType;
use App\Repository\HistoriqueRepository;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;




#[Route('/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/tri', name: 'app_service_tri', methods: ['GET'])]
    public function tri(Request $request, ServiceRepository $serviceRepository): Response
    {
        $sortField = $request->query->get('sort', 'name'); // 'name' est le tri par défaut
        $sortDirection = $request->query->get('direction', 'ASC'); // 'ASC' est la direction par défaut
        
        // Assurez-vous que les paramètres de tri sont valides
        $validSortFields = ['name', 'description', 'dateDeCreation']; // Ajoutez tous les champs valides de l'entité Service ici
        $validDirections = ['ASC', 'DESC'];
    
        if (!in_array($sortField, $validSortFields) || !in_array($sortDirection, $validDirections)) {
            throw new \InvalidArgumentException('Invalid sort field or direction');
        }
    
        $services = $serviceRepository->findBySortedField($sortField, $sortDirection);
    

    
        return $this->render('service/index.html.twig', [
            'services' => $services,
        ]);
    }
    
    
    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $service->setEtat(1);
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            $historique = new Historique();
            $historique->setDescription("New service created: " . $service->getName());
            $historique->setCreatedAt(new \DateTime());
            $entityManager->persist($historique);
            $entityManager->flush();
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service, EntityManagerInterface $entityManager): Response
    {
        $ratings = $entityManager->getRepository(Rating::class)->findByService($service);
    
        return $this->render('service/show.html.twig', [
            'service' => $service,
            'ratings' => $ratings,
        ]);
    }
    

        #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
        public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(ServiceType::class, $service);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
            }
            $historique = new Historique();
        $historique->setDescription("Service edited: " . $service->getName());
        $historique->setCreatedAt(new \DateTime());
        $entityManager->persist($historique);

        $entityManager->flush();

            return $this->render('service/edit.html.twig', [
                'service' => $service,
                'form' => $form->createView(),
            ]);
        }
     #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
public function delete(Request $request, Service $service, EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository): Response
{
    if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
        $name = $service->getName(); // Store the name before deleting for historique logging

        $historique = new Historique();
        $historique->setDescription("Deleted service: " . $name);
        $historique->setCreatedAt(new \DateTime());

        $entityManager->persist($historique);
        $entityManager->remove($service);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
}

#[Route('/export/excel', name: 'app_service_export_excel')]
public function exportExcel(ServiceRepository $serviceRepository): Response
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Define the Excel file's headers
    $headers = ['ID', 'Name', 'Description', 'Date of Creation', 'State'];

    // Set headers to the sheet
    $column = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($column++ . '1', $header);
    }

    // Fetch all services from the database
    $services = $serviceRepository->findAll();
    $rowNumber = 2; // Start from the second row after the headers

    // Fill data to the sheet
    foreach ($services as $service) {
        $sheet->setCellValue('A' . $rowNumber, $service->getId());
        $sheet->setCellValue('B' . $rowNumber, $service->getName());
        $sheet->setCellValue('C' . $rowNumber, $service->getDescription());
        $sheet->setCellValue('D' . $rowNumber, $service->getDateDeCreation()->format('Y-m-d'));
        $sheet->setCellValue('E' . $rowNumber, $service->getEtat());
        $rowNumber++;
    }

    // Create the Excel file writer
    $writer = new Xlsx($spreadsheet);

    // Create the StreamedResponse to download the Excel file
    $response = new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    });

    // Define the response headers
    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
    $response->headers->set('Content-Disposition', 'attachment;filename="service_list.xlsx"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}


#[Route('/{id}/rate', name: 'service_rate', methods: ['GET', 'POST'])]
public function rateService(Request $request, Service $service, EntityManagerInterface $entityManager): Response
{
    $rating = new Rating();
    $rating->setService($service); // Set the service for the rating
    $form = $this->createForm(RatingType::class, $rating);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $rating = $form->getData();
        $entityManager->persist($rating);
        $entityManager->flush();
        return $this->redirectToRoute('app_service_show', ['id' => $service->getId()]);

    }

    return $this->render('service/rate.html.twig', [
        'service' => $service,
        'form' => $form->createView(),
        
    ]);
}
#[Route('/{id}/qr-code', name: 'service_qr_code', methods: ['GET'])]
public function generateQrCode(Service $service): Response
{
    $url = $this->generateUrl('app_service_show', ['id' => $service->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    $qrCode = new QrCode($url);
    $qrCode->setSize(300);

    // Assuming you're using a recent version of the library,
    // use the 'writeDataUri()' or 'writeString()' method according to your library's version.
    // The 'writeString()' method should be used with a writer, as follows:

    // Create a response object and set the content type to 'image/png'
    $response = new Response();
    $response->headers->set('Content-Type', 'image/png');

    // Use the appropriate writer to output the QR code as PNG
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $pngData = $writer->write($qrCode)->getString();

    $response->setContent($pngData);

    return $response;
}


}
