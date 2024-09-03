<?php

namespace App\Interface;

use App\DTO\TransactionDTO;
use App\Model\TransactionResponseModel;

interface PaymentProcessorInterface
{
    public function getName(): string;

    public function makeTransaction(TransactionDTO $transactionDTO): TransactionResponseModel;
}