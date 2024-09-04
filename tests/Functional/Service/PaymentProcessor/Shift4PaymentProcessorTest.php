<?php

namespace App\Tests\Functional\Service\PaymentProcessor;

use App\DTO\TransactionDTO;
use App\Model\TransactionResponseModel;
use App\Service\PaymentProcessor\Shift4PaymentProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Shift4PaymentProcessorTest extends KernelTestCase
{
    public function testMakeTransactionReturnsCorrectResponse()
    {
        self::bootKernel();
        $shift4ProcessorService = self::getContainer()->get(Shift4PaymentProcessor::class);
        $date                   = (new \DateTime())->modify('+5 years');
        $transactionDTO         = new TransactionDTO(
            cardNumber: '4200000000000000',
            cardExpiryYear: $date->format('Y'),
            cardExpiryMonth: $date->format('m'),
            cardCvv: '123',
            amount: '10',
            currency: 'EUR'
        );

        $transaction = $shift4ProcessorService->makeTransaction($transactionDTO);

        $this->assertInstanceOf(TransactionResponseModel::class, $transaction);
        $this->assertEquals($transactionDTO->getCurrency(), $transaction->getTransactionCurrency());
        $this->assertEquals($transactionDTO->getAmount(), $transaction->getTransactionAmount());
    }
}
