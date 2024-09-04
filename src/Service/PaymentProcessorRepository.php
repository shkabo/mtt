<?php

namespace App\Service;

use App\Exception\PaymentProcessorException;
use App\Interface\PaymentProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class PaymentProcessorRepository
{
    private array $gateways = [];

    public function __construct(#[TaggedIterator('payment_gateway')] iterable $paymentGateways)
    {
        foreach ($paymentGateways as $paymentGateway) {
            $this->gateways[$paymentGateway->getName()] = $paymentGateway;
        }
    }

    public function getPaymentGateway(string $name): PaymentProcessorInterface
    {
        if (!array_key_exists($name, $this->gateways)) {
            throw new PaymentProcessorException("Payment gateway '$name' not found");
        }

        return $this->gateways[$name];
    }

    public function getPaymentGatewayNames(): array
    {
        return array_keys($this->gateways);
    }
}
