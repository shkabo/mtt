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

class Shift4PaymentProcessor implements PaymentProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $shift4Client
    ) {
    }

    public function getName(): string
    {
        return 'shift4';
    }

    /**
     * @throws PaymentProcessorException
     */
    public function makeTransaction(TransactionDTO $transactionDTO): TransactionResponseModel
    {
        $charge = $this->createCharge($transactionDTO);

        return new TransactionResponseModel(
            transactionId: $charge['id'],
            transactionAmount: $charge['amount'] / 100,
            transactionCurrency: $charge['currency'],
            cardBin: $charge['card']['id'],
            transactionDate: new \DateTimeImmutable($charge['created']),
        );
    }

    private function createCharge(TransactionDTO $transactionDTO): array
    {
        try {
            $response = $this->shift4Client->request('POST', '/charges', [
                'body' => [
                    'amount' => $transactionDTO->getAmount() * 100, // in cents
                    'currency' => $transactionDTO->getCurrency(),
                    'card' => [
                        'number' => $transactionDTO->getCardNumber(),
                        'expMonth' => $transactionDTO->getCardExpiryMonth(),
                        'expYear' => $transactionDTO->getCardExpiryYear(),
                        'cvc' => $transactionDTO->getCardCvv(),
                    ],
                ]
            ]);

            // we can do all kinds of checks in here
            // e.g. check status from returned json object if it's successful etc.
            // for the sake of this app we'll keep it simple and do some basic checks
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid response code: '. $response->getStatusCode());
            }

        } catch (\Exception|ExceptionInterface|ClientException|TransportExceptionInterface $e) {
            $this->logger->error('[Shift4PaymentProcessor] API operation failed: ' . $e->getMessage(), ['transactionDTO' => $transactionDTO]);
            throw new PaymentProcessorException('There was an error processing your request', previous: $e);
        }

        return $response->toArray();
    }
}