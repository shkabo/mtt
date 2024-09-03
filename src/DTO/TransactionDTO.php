<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TransactionDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\CardScheme(
            schemes: [
            Assert\CardScheme::VISA,
            Assert\CardScheme::MASTERCARD,
            Assert\CardScheme::DISCOVER
                ],
        )]
        private readonly string $cardNumber,

        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y')]
        private readonly string $cardExpiryYear,

        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'm')]
        private readonly string $cardExpiryMonth,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 3)]
        private readonly int $cardCvv,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        private readonly float|int $amount = 0,

        #[Assert\NotBlank]
        #[Assert\Currency]
        #[Assert\Choice(choices: ['USD', 'EUR'])]
        private readonly string $currency = 'EUR',
    ) {
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getCardExpiryYear(): string
    {
        return $this->cardExpiryYear;
    }

    public function getCardExpiryMonth(): string
    {
        return $this->cardExpiryMonth;
    }

    public function getCardCvv(): int
    {
        return $this->cardCvv;
    }

    public function getAmount(): float|int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}