<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\Institution;
use App\Entity\Intake;
use App\Entity\Student;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-demo',
    description: 'Add a demo data.',
)]
class CreateDemoDataCommand extends Command
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

        // yoobee
        // aut
        $yoobee = new Institution();
        $yoobee->setName('Yoobee College of Creative Innovation');
        $yoobee->setLocation('Auckland City Road Campus, New Zealand');
        $this->entityManager->persist($yoobee);

        $aut = new Institution();
        $aut->setName('Auckland University of Technology');
        $aut->setLocation('55 Wellesley Street East, Auckland Central, New Zealand');
        $this->entityManager->persist($aut);


        $course_y_mse = new Course();
        $course_y_mse->setName('Master of Software Engineering');
        $course_y_mse->setDuration(new \DateInterval('P1Y'));
        $course_y_mse->setInstitution($yoobee);
        $this->entityManager->persist($course_y_mse);


        $course_y_b = new Course();
        $course_y_b->setName('Bachelor of Software Engineering');
        $course_y_b->setDuration(new \DateInterval('P3Y'));
        $course_y_b->setInstitution($yoobee);
        $this->entityManager->persist($course_y_b);

        $course_y_dc = new Course();
        $course_y_dc->setName('Diploma in Cloud Engineering');
        $course_y_dc->setDuration(new \DateInterval('P8M'));
        $course_y_dc->setInstitution($yoobee);
        $this->entityManager->persist($course_y_dc);

        //courses aut
        $subjects_mse = [
            ['Professional Software Engineering', 'MSE800'],
            ['Research Methods', 'MSE801'],
            ['Quantum Computing', 'MSE802'],
            ['Data Analytics', 'MSE803'],
            ['Blockchain and decentralised digital identity', 'MSE804'],
            ['Cloud Security', 'MSE805'],
            ['Intelligent Transportation Systems', 'MSE806'],
            ['Industry-based capstone research project', 'MSE907'],
        ];
        foreach ($subjects_mse as $subjectData) {
            $subject_mse1 = new Subject();
            $subject_mse1->setName($subjectData[0]);
            $subject_mse1->setCode($subjectData[1]);
            $subject_mse1->setCourse($course_y_mse);
            $this->entityManager->persist($subject_mse1);
        }

        //Subjects aut


        //Intakes yoobee
        $intakes_mse_yoobee = [
            [
                'MSE 2311',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2023-11-13'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-11-08'),
            ],
            [
                'MSE 2307',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2023-07-13'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-07-08'),
            ],
        ];
        foreach ($intakes_mse_yoobee as $intakeData) {
            $intake = new Intake();
            $intake->setName($intakeData[0]);
            $intake->setStart($intakeData[1]);
            $intake->setFinish($intakeData[2]);
            $intake->setCourse($course_y_mse);
            $this->entityManager->persist($intake);
        }

        //Intakes aut

        //Periods yoobee
        //Periods aut

        //Students yoobee
        //Students aut

        //Teachers yoobee
        //Teachers aut

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
            $user->setGender($studentItem[3]);
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
