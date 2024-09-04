<?php

namespace App\Service;

use App\DTO\TransactionDTO;
use App\Exception\PaymentProcessorException;
use App\Model\TransactionResponseModel;

class TransactionService
{
    public function __construct(
        private readonly PaymentProcessorRepository $paymentProcessorRepository
    ) {
    }

    /**
     * @throws PaymentProcessorException
     */
    public function doPurchase(string $paymentGateway, TransactionDTO $transactionDTO): TransactionResponseModel
    {
        $gateway = $this->paymentProcessorRepository->getPaymentGateway($paymentGateway);

        return $gateway->makeTransaction($transactionDTO);
    }

    public function getPaymentProcessorRepository(): PaymentProcessorRepository
    {
        return $this->paymentProcessorRepository;
    }

    public function doRefund()
    {
        // some logic here to do a refund via one of our providers
    }
}
