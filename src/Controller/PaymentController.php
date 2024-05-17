<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;
use Stripe\Charge;

class PaymentController extends AbstractController
{
    #[Route('/charge', name: 'charge')]
    public function charge(Request $request): Response
    {
        $stripeSecretKey = $this->getParameter('stripe_secret_key');
        Stripe::setApiKey($stripeSecretKey);

        $token = $request->request->get('stripeToken');

        if (!$token) {
            // Redirect to the failure page with an appropriate flash message
            $this->addFlash('error', 'Stripe token not provided.');
            return $this->redirectToRoute('payment_failure');
        }

        try {
            $charge = Charge::create([
                'amount' => 1000, // This amount should be dynamic or come from your service entity
                'currency' => 'eur',
                'description' => 'Example charge',
                'source' => $token,
            ]);

            // TODO: Save the charge details to your database

            // Previously: Check if the charge was successful and redirect. Now removed.
            // The logic here can be adjusted depending on how you want to handle post-charge actions without a success redirect.
            
        } catch (ApiErrorException $e) {
            // Log the API error if needed
            $this->addFlash('error', 'Payment failed: ' . $e->getMessage());
            return $this->redirectToRoute('payment_failure');
        }
        
        // Optionally, direct to a generic page or the payment failure page if you don't distinguish between success and failure via redirect.
        return $this->redirectToRoute('payment_failure');
    }

    #[Route('/payment/failure', name: 'payment_failure')]
    public function paymentFailure(): Response
    {
        // Render the failure page with any flash messages set earlier
        return $this->render('payment/failure.html.twig');
    }

    #[Route('/checkout/{service_id}', name: 'checkout')]
    public function checkout(Request $request, int $service_id): Response
    {
        // Ici, récupérez les informations de service si nécessaire
        // ...

        return $this->render('payment/checkout.html.twig', [
            // 'service' => $service, // Passez les données nécessaires à votre template
        ]);
    }
}
