<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\isEmpty;


#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new api user',
)]
class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
            ->addArgument('role', InputArgument::OPTIONAL, 'The role of the user', 'ROLE_USER');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');

        if (!$this->validateEmail($email, $io)) {
            return Command::FAILURE;
        }

        $user = $this->createUser($email, $password, $role);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User successfully created! Email: %s', $email));

        return Command::SUCCESS;
    }

    private function validateEmail(string $email, SymfonyStyle $io): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('The email is not valid.');
            return false;
        }

        return true;
    }

    private function createUser(string $email, string $password, string $role): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }
}
