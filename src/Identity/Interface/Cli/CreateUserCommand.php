<?php

namespace App\Identity\Interface\Cli;

use App\Identity\Application\Command\RegisterUserCommand;
use App\Identity\Domain\Exception\EmailAlreadyTakenException;
use App\Identity\Infrastructure\Security\SecurityUser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsCommand(name: 'app:create-user', description: 'Creates a new user.')]
class CreateUserCommand extends Command
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
        $this->messageBus = $messageBus;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getOption('email');
        $password = (string) $input->getOption('password');

        if ($email === '' || $password === '') {
            $io->error('Both --email and --password are required.');
            return Command::INVALID;
        }

        $hashed = $this->passwordHasherFactory->getPasswordHasher(SecurityUser::class)->hash($password);

        try {
            $this->handle(new RegisterUserCommand($email, $hashed));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious() ?? $e;
            if ($previous instanceof EmailAlreadyTakenException) {
                $io->error($previous->getMessage());
                return Command::FAILURE;
            }
            throw $e;
        }

        $io->success('User created successfully!');

        return Command::SUCCESS;
    }
}
