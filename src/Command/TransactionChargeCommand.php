<?php

namespace App\Command;

use App\DTO\TransactionDTO;
use App\Exception\PaymentProcessorException;
use App\Service\TransactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validation;

#[AsCommand(
    name: 'app:transaction-charge',
    description: 'Make Transaction charge via provided payment processor',
)]
class TransactionChargeCommand extends Command
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('listProcessors', null, InputOption::VALUE_NONE, 'Outputs list of available payment processors')
            ->addArgument('paymentProcessor', InputArgument::OPTIONAL, 'Payment Processor to use')
            ->addOption('amount', null, InputOption::VALUE_OPTIONAL, 'Amount to be charged')
            ->addOption('currency', null, InputOption::VALUE_OPTIONAL, 'Currency to charge (e.g. USD/EUR)')
            ->addOption('cardNumber', null, InputOption::VALUE_OPTIONAL, 'Valid card number (no spaces)')
            ->addOption('cardExpiryYear', null, InputOption::VALUE_OPTIONAL, 'Card expiration year')
            ->addOption('cardExpiryMonth', null, InputOption::VALUE_OPTIONAL, 'Card expiration month')
            ->addOption('cardCvv', null, InputOption::VALUE_OPTIONAL, 'Card cvv')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // output list of available Payment Processors that we have in our repository
        if ($input->getOption('listProcessors')) {
            $io->title('Payment Processors');
            $io->listing($this->transactionService->getPaymentProcessorRepository()->getPaymentGatewayNames());

            return Command::SUCCESS;
        }

        // paymentProcessor argument is not passed, show a note that it's required
        if (!$input->getArgument('paymentProcessor')) {
            $io->note('You must provide a payment processor or check --help for more info');

            return Command::FAILURE;
        }

        $paymentProcessor = $input->getArgument('paymentProcessor');
        $validator        = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        // check if we have all parameters so that we can make transaction
        $missing = false;
        foreach (['amount', 'currency', 'cardNumber', 'cardExpiryYear', 'cardExpiryMonth', 'cardCvv'] as $option) {
            if (!$input->getOption($option)) {
                $missing = true;
                $io->warning("Option '{$option}' is required");
            }
        }

        if ($missing) {
            return Command::FAILURE;
        }

        // build DTO
        // we should add more validation for input from the CLI before trying to build object
        // but for the sake of the test, we'll assume that input will be correct
        $transactionDto = new TransactionDTO(
            cardNumber: $input->getOption('cardNumber'),
            cardExpiryYear: $input->getOption('cardExpiryYear'),
            cardExpiryMonth: $input->getOption('cardExpiryMonth'),
            cardCvv: $input->getOption('cardCvv'),
            amount: $input->getOption('amount'),
            currency: $input->getOption('currency'),
        );

        // validate our DTO object
        $errors = $validator->validate($transactionDto);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }

            return Command::FAILURE;
        }

        // make transaction
        try {
            $transactionResult = $this->transactionService
                ->doPurchase($paymentProcessor, $transactionDto);

            // add some styling to the output
            $io->success('Charge successful');
            $table = $io->createTable();
            $table->setHeaderTitle('Transaction result');
            $table->setHeaders(['Field', 'Value']);
            foreach ($transactionResult->toArray() as $field => $value) {
                $table->addRow([$field, $value]);
            }
            $table->render();

            return Command::SUCCESS;
        } catch (PaymentProcessorException $e) {
            $io->error($e->getMessage());
        }

        return Command::FAILURE;
    }
}
