<?php

namespace App\Command;

use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-teacher',
    description: 'Add an teacher user.',
)]
class CreateTeacherCommand extends Command
{
    private const EMAIL = 'teacher@example.com';
    private const PASS = 'teacher';
    private const NAME = 'John';
    private const SURNAME = 'Teacher';

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
        $repository = $this->entityManager->getRepository(Teacher::class);

        if ($repository->count(['email' => self::EMAIL]) > 0) {
            $io->success(
                'User already exists in your database.'
            );

            return Command::SUCCESS;
        }

        $user = new Teacher();
        $user->setEmail(self::EMAIL);
        $user->setName(self::NAME);
        $user->setSurname(self::SURNAME);

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                self::PASS
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(
            'You can now login, email = ' . self::EMAIL . ' and password = ' . self::PASS
        );

        return Command::SUCCESS;
    }
}
