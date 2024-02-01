<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Add an admin user.',
)]
class CreateAdminCommand extends Command
{
    private const EMAIL = 'admin@example.com';
    private const PASS = 'admin';

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $this->entityManager->getRepository(Admin::class);

        if ($repository->count(['email' => self::EMAIL]) > 0) {
            $io->success(
                'Admin user already exists in your database.'
            );

            return Command::SUCCESS;
        }

        $user = new Admin(
            self::EMAIL,
        );

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                self::PASS
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(
            'You can now login as admin, email = ' . self::EMAIL . ' and password = ' . self::PASS
        );

        return Command::SUCCESS;
    }
}
