<?php

namespace App\Controller\Api;

use App\DTO\TransactionDTO;
use App\Exception\PaymentProcessorException;
use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction/charge/{paymentGateway}', name: 'app_transaction', requirements: ['paymentGateway' => 'aci|shift4'], methods: ['POST'])]
    public function index(string $paymentGateway, #[MapRequestPayload] TransactionDTO $transactionDTO, TransactionService $transactionService): JsonResponse
    {
        try {
            $response = $transactionService->doPurchase($paymentGateway, $transactionDTO);
        } catch (PaymentProcessorException $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        return $this->json($response, 200);
    }
}
