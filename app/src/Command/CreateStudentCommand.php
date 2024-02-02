<?php

namespace App\Command;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-student',
    description: 'Add an student user.',
)]
class CreateStudentCommand extends Command
{
    private const EMAIL = 'student@example.com';
    private const PASS = 'student';
    private const NAME = 'Steve';
    private const SURNAME = 'Student';

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
        $repository = $this->entityManager->getRepository(Student::class);

        if ($repository->count(['email' => self::EMAIL]) > 0) {
            $io->success(
                'User already exists in your database.'
            );

            return Command::SUCCESS;
        }

        $user = new Student();
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
