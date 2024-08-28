<?php

namespace App\News\Interface\Cli;

use App\News\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public static function getDefaultName(): ?string {
        return self::$defaultName;
    }

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getOption('email');
        $password = $input->getOption('password');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('User created successfully!');

        return Command::SUCCESS;
    }
}
