<?php

namespace App\Service;

use App\DTO\TransactionDTO;
use App\Model\TransactionResponseModel;

class TransactionService
{
    public function __construct(
        private readonly PaymentProcessorRepository $gatewayRepository
    ) {
    }

    public function doPurchase(string $paymentGateway, TransactionDTO $transactionDTO): TransactionResponseModel
    {
        $gateway = $this->gatewayRepository->getPaymentGateway($paymentGateway);

        return $gateway->makeTransaction($transactionDTO);
    }

    public function doRefund()
    {
        // some logic here to do a refund via one of our providers
    }
}