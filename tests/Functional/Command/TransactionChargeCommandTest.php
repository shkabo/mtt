<?php

namespace App\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Console\Test\InteractsWithConsole;

class TransactionChargeCommandTest extends KernelTestCase
{
    use InteractsWithConsole;

    public function testTransactionCommandChargeListPaymentProvidersOption()
    {
        $this->consoleCommand('app:transaction-charge')
            ->addOption('listProcessors')
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Payment Processors');
    }

    public function testTransactionCommandChargeMissingPaymentProcessorOption()
    {
        $this->consoleCommand('app:transaction-charge')
            ->execute()
            ->assertFaulty()
            ->assertOutputContains('You must provide a payment processor');
    }

    public function testTransactionCommandChargeMissingTransactionParameters()
    {
        $this->consoleCommand('app:transaction-charge')
            ->addArgument('aci')
            ->execute()
            ->assertFaulty()
            ->assertOutputContains("Option 'amount' is required")
            ->assertOutputContains("Option 'currency' is required")
            ->assertOutputContains("Option 'cardNumber' is required")
            ->assertOutputContains("Option 'cardExpiryYear' is required")
            ->assertOutputContains("Option 'cardExpiryMonth' is required")
            ->assertOutputContains("Option 'cardCvv' is required")
        ;
    }

    public function testTransactionCommandChargeSuccess()
    {
        $date = (new \DateTime())->modify('+5 years');
        $this->consoleCommand('app:transaction-charge')
            ->addArgument('aci')
            ->addOption('amount', '12')
            ->addOption('currency', 'EUR')
            ->addOption('cardNumber', '4200000000000000')
            ->addOption('cardExpiryYear', $date->format('Y'))
            ->addOption('cardExpiryMonth', $date->format('m'))
            ->addOption('cardCvv', '123')
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Charge successful')
            ->assertOutputContains('transactionId')
            ->assertOutputContains('transactionAmount')
            ->assertOutputContains('transactionCurrency')
            ->assertOutputContains('cardBin')
            ->assertOutputContains('transactionDate')
        ;
    }

    public function testTransactionCommandChargeInvalidOptionData()
    {
        $date = (new \DateTime())->modify('+5 years');
        $this->consoleCommand('app:transaction-charge')
            ->addArgument('aci')
            ->addOption('amount', '12')
            ->addOption('currency', 'RSD')
            ->addOption('cardNumber', '4200000000000000')
            ->addOption('cardExpiryYear', '2004')
            ->addOption('cardExpiryMonth', $date->format('m'))
            ->addOption('cardCvv', '123')
            ->execute()
            ->assertFaulty()
            ->assertOutputContains('Card Year must be a valid year and not in the past')
            ->assertOutputContains('The value you selected is not a valid choice')
        ;
    }
}
