<?php

namespace App\Model;

class TransactionResponseModel
{
    public function __construct(
        private readonly string $transactionId,
        private readonly string $transactionAmount,
        private readonly string $transactionCurrency,
        private readonly string $cardBin,
        private readonly \DateTimeImmutable $transactionDate = new \DateTimeImmutable('now')
    )
    {
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getTransactionAmount(): string
    {
        return $this->transactionAmount;
    }

    public function getTransactionCurrency(): string
    {
        return $this->transactionCurrency;
    }

    public function getCardBin(): string
    {
        return $this->cardBin;
    }

    public function getTransactionDate(): \DateTimeImmutable
    {
        return $this->transactionDate;
    }

}