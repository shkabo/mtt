<?php

namespace App\Service\PaymentProcessor;

use App\DTO\TransactionDTO;
use App\Exception\PaymentProcessorException;
use App\Interface\PaymentProcessorInterface;
use App\Model\TransactionResponseModel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AciPaymentProcessor implements PaymentProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    // hardcoded values as requested
    private string $entityId = '8a8294174b7ecb28014b9699220015ca';
    private string $paymentBrand = 'VISA';
    private string $cardNumber = '4200000000000000';
    private string $currency = 'EUR';

    public function __construct(
        private readonly HttpClientInterface $aciClient,
    ) {
    }

    public function getName(): string
    {
        return 'aci';
    }

    /**
     * @throws PaymentProcessorException
     */
    public function makeTransaction(TransactionDTO $transactionDTO): TransactionResponseModel
    {
        $payment = $this->serverToServerDebitPayment($transactionDTO);

        return new TransactionResponseModel(
            transactionId: $payment['id'],
            transactionAmount: $payment['amount'],
            transactionCurrency: $payment['currency'],
            cardBin: $payment['card']['bin'],
            transactionDate: new \DateTimeImmutable($payment['timestamp']),
        );
    }

    /**
     * @throws PaymentProcessorException
     */
    private function serverToServerDebitPayment(TransactionDTO $transactionDTO)
    {
        try {
            $response = $this->aciClient->request('POST', '/v1/payments', [
                'body' => [
                    'entityId' => $this->entityId,
                    'amount' => number_format($transactionDTO->getAmount(), 2),
                    'currency' => $this->currency,
                    'paymentBrand' => $this->paymentBrand,
                    'paymentType' => 'DB',
                    'card.number' => $this->cardNumber,
                    'card.holder' => 'Jane Jones',
                    'card.expiryMonth' => $transactionDTO->getCardExpiryMonth(),
                    'card.expiryYear' => $transactionDTO->getCardExpiryYear(),
                    'card.cvv' => (string) $transactionDTO->getCardCvv()
                ]
            ]);

            // we can do all kinds of checks in here
            // e.g. check result code from response json object if it's successful https://eu-test.oppwa.com/v1/resultcodes
            // check if their api returns response object regardless of the status code etc.
            // for the sake of this app we'll keep it simple and do some basic checks
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid response code: '. $response->getStatusCode());
            }

        } catch (\Exception|ExceptionInterface|ClientException|TransportExceptionInterface $e) {
            $this->logger->error('[AciPaymentProcessor] API operation failed: ' . $e->getMessage(), ['transactionDTO' => $transactionDTO]);
            throw new PaymentProcessorException('There was an error processing your request', previous: $e);
        }

        return $response->toArray();
    }
}