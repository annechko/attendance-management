<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\Institution;
use App\Entity\Intake;
use App\Entity\Period;
use App\Entity\PeriodToSubject;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TeacherToSubjectToIntake;
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
        if ($this->entityManager->getRepository(Institution::class)->findOneBy(
            ['name' => 'Yoobee College of Creative Innovation']
        )) {
            $io->success(
                'Already has data, skipping.'
            );

            return Command::SUCCESS;
        }
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
            ['Professional Software Engineering', 'MSE800', 0, 1, 30],
            ['Research Methods', 'MSE801', 0, 2, 15],
            ['Quantum Computing', 'MSE802', 0, 0, 15],
            ['Data Analytics', 'MSE803', 1, 0, 15],
            ['Blockchain and decentralised digital identity', 'MSE804', 1, 1, 15],
            ['Cloud Security', 'MSE805', 1, 2, 15],
            ['Intelligent Transportation Systems', 'MSE806', 1, 0, 15],
            ['Industry-based capstone research project', 'MSE907', 2, 1, 60],
        ];
        $subjects = [];
        foreach ($subjects_mse as $subjectData) {
            $subject_mse1 = new Subject();
            $subjects[] = $subject_mse1;
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
        $intakes = [];
        foreach ($intakes_mse_yoobee as $intakeData) {
            $intake = new Intake();
            $intakes[] = $intake;
            $intake->setName($intakeData[0]);
            $intake->setStart($intakeData[1]);
            $intake->setFinish($intakeData[2]);
            $intake->setCourse($course_y_mse);
            $this->entityManager->persist($intake);
        }

        //Intakes aut

        //Periods yoobee
        $periods_mse_yoobee = [
            [
                'Trimester 1',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2023-11-13'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-03-15'),
                $intakes[0],
            ],
            [
                'Trimester 2',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-03-15'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-09-12'),
                $intakes[0],
            ],
            [
                'Trimester 3',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-09-12'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-11-08'),
                $intakes[0],
            ],
        ];
        $periods = [];
        foreach ($periods_mse_yoobee as $periodData) {
            $period = new Period();
            $periods[] = $period;
            $period->setName($periodData[0]);
            $period->setStart($periodData[1]);
            $period->setFinish($periodData[2]);
            $period->setIntake($periodData[3]);
            $this->entityManager->persist($period);
        }
        //Periods aut

        //period to subject
        foreach ($subjects_mse as $index => $subjectData) {
            $entity = new PeriodToSubject();
            $entity->setSubject($subjects[$index]);
            $entity->setPeriod($periods[$subjectData[2]]);
            $entity->setTotalNumberOfLessons($subjectData[4]);
            $this->entityManager->persist($entity);
        }

        //Students yoobee
        $studentsData = file_get_contents('/app/src/Command/students');
        $studentsData = explode("\n", $studentsData);

        foreach ($studentsData as $studentItem) {
            $studentItem = explode(',', $studentItem);

            $user = new Student();
            $user->setIntake($intakes[0]);
            $user->setName($studentItem[1]);
            $user->setSurname($studentItem[2]);
            $user->setGender($studentItem[3]);
            $user->setEmail($studentItem[0]);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    self::PASS
                )
            );

            $this->entityManager->persist($user);
        }
        //Students aut

        //Teachers yoobee
        $teachers_yoobee = [
            [
                'Oscar',
                'Harrell',
                'oscar.harrell@example.com',
            ],
            [
                'Karen',
                'Flynn',
                'karen.flynn@example.com',
            ],
            [
                'Donovan',
                'Morton',
                'donovan.morton@example.com',
            ],

        ];
        $teachers = [];

        foreach ($teachers_yoobee as $entityData) {
            $entity = new Teacher();
            $teachers[] = $entity;
            $entity->setName($entityData[0]);
            $entity->setSurname($entityData[1]);
            $entity->setEmail($entityData[2]);
            $entity->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $entity,
                    'teacher'
                )
            );
            $this->entityManager->persist($entity);
        }
        //Teachers aut


        //teacher subject intake
        foreach ($subjects_mse as $index => $subjectData) {
            $entity = new TeacherToSubjectToIntake();
            $entity->setSubject($subjects[$index]);
            $entity->setIntake($intakes[0]);
            $entity->setTeacher($teachers[$subjectData[3]]);
            $entity->setStart($intakes[0]->getStart());
            $entity->setFinish($intakes[0]->getFinish());
            $this->entityManager->persist($entity);
        }


        $this->entityManager->flush();

        $io->success(
            'You can now login as student with password = ' . self::PASS
        );

        return Command::SUCCESS;
    }
}
