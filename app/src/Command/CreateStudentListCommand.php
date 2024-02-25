<?php

namespace App\Command;

use App\Entity\Intake;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-student-list',
    description: 'Add a student user.',
)]
class CreateStudentListCommand extends Command
{
    private const PASS = 'student';

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
        $intake = $this->entityManager->getRepository(Intake::class)->createQueryBuilder('c')
            ->orderBy('c.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $studentsData = file_get_contents('/app/src/Command/students');
        $studentsData = explode("\n", $studentsData);

        foreach ($studentsData as $studentItem) {
            $studentItem = explode(',', $studentItem);
            if ($repository->count(['email' => $studentItem[0]]) > 0) {
                $io->write(
                    "User \"$studentItem[0]\" already exists in your database, skipping.\n"
                );

                continue;
            }
            $user = new Student();
            $user->setIntake($intake);
            $user->setEmail($studentItem[0]);
            $user->setName($studentItem[1]);
            $user->setSurname($studentItem[2]);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    self::PASS
                )
            );

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $io->success(
            'You can now login as student with password = ' . self::PASS
        );

        return Command::SUCCESS;
    }
}
