<?php

namespace App\Tests\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;

class TransactionControllerTest extends KernelTestCase
{
    use HasBrowser;

    public function testCreateACITransactionChargeEndpoint()
    {
        $this->browser()->post('/api/transaction/charge/aci', ['body' => $this->getPostData()])
            ->assertStatus(200)
            ->assertJson()
            ->use(function(\Zenstruck\Browser\Json $json) {
                $json->assertHas('transactionId');
                $json->assertHas('transactionAmount');
                $json->assertHas('transactionCurrency');
                $json->assertHas('cardBin');
                $json->assertHas('transactionDate');
            });
    }

    public function testCreateShift4TransactionChargeEndpoint()
    {
        $this->browser()->post('/api/transaction/charge/shift4', ['body' => $this->getPostData()])
            ->assertStatus(200)
            ->assertJson()
            ->use(function(\Zenstruck\Browser\Json $json) {
                $json->assertHas('transactionId');
                $json->assertHas('transactionAmount');
                $json->assertHas('transactionCurrency');
                $json->assertHas('cardBin');
                $json->assertHas('transactionDate');
            });
    }

    public function testInvalidParametersTransactionChargeEndpoint()
    {
        $date = (new \DateTime())->modify('+5 years');
        $data = [
            'cardNumber' => '4200000000000000',
            'cardExpiryYear' => $date->format('Y'),
            'cardExpiryMonth' => '123', // invalid date
            'cardCvv'   => '123',
            'amount'    => '12',
            'currency'  => 'EUR',
        ];

        $this->browser()->post('/api/transaction/charge/shift4', ['body' => $data])
            ->assertStatus(422);

        $this->browser()->post('/api/transaction/charge/aci', ['body' => $data])
            ->assertStatus(422);
    }

    private function getPostData(): array
    {
        $date = (new \DateTime())->modify('+5 years');
        return [
            'cardNumber' => '4200000000000000',
            'cardExpiryYear' => $date->format('Y'),
            'cardExpiryMonth' => $date->format('m'),
            'cardCvv'   => '123',
            'amount'    => '12',
            'currency'  => 'EUR',
        ];
    }
}