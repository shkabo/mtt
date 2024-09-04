<?php

namespace App\Tests\Functional\Service\PaymentProcessor;

use App\DTO\TransactionDTO;
use App\Model\TransactionResponseModel;
use App\Service\PaymentProcessor\AciPaymentProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AciPaymentProcessorTest extends KernelTestCase
{
    public function testMakeTransactionReturnsCorrectResponse()
    {
        self::bootKernel();
        $aciProcessorService = self::getContainer()->get(AciPaymentProcessor::class);
        $date                = (new \DateTime())->modify('+5 years');
        $transactionDTO      = new TransactionDTO(
            cardNumber: '4200000000000000',
            cardExpiryYear: $date->format('Y'),
            cardExpiryMonth: $date->format('m'),
            cardCvv: '123',
            amount: '10',
            currency: 'EUR'
        );

        $transaction = $aciProcessorService->makeTransaction($transactionDTO);

        $this->assertInstanceOf(TransactionResponseModel::class, $transaction);
        $this->assertEquals($transactionDTO->getCurrency(), $transaction->getTransactionCurrency());
        $this->assertEquals(number_format($transactionDTO->getAmount(), 2), $transaction->getTransactionAmount());
    }
}
