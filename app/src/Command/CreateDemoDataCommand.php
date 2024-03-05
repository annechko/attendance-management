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
        $this->entityManager->getConnection()->executeStatement("
TRUNCATE table teacher_to_subject_to_intake;
TRUNCATE table period_to_subject CASCADE ;
TRUNCATE table period CASCADE ;
TRUNCATE table intake CASCADE ;
TRUNCATE table course CASCADE ;
TRUNCATE table institution CASCADE ;
TRUNCATE table student CASCADE ;
TRUNCATE table attendance CASCADE ;
TRUNCATE table subject CASCADE ;
TRUNCATE table teacher CASCADE ;");

        //Teachers
        $teachers_data = [
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
            [//aut
                'Ryan',
                'Borenstein',
                'ryan.borenstein@example.com',
            ],
            [
                'Kendra',
                'Markette',
                'kendra.markette@example.com',
            ],
            [
                'Finley',
                'Naruaez',
                'finley.naruaez@example.com',
            ],

        ];
        $teachers = [];

        foreach ($teachers_data as $entityData) {
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

        $course_a_ba = new Course();
        $course_a_ba->setName('Bachelor of Architecture and Future Environments');
        $course_a_ba->setDuration(new \DateInterval('P3Y'));
        $course_a_ba->setInstitution($aut);
        $this->entityManager->persist($course_a_ba);

        $course_a_ms = new Course();
        $course_a_ms->setName('Master of Engineering Project Management');
        $course_a_ms->setDuration(new \DateInterval('P1Y'));
        $course_a_ms->setInstitution($aut);
        $this->entityManager->persist($course_a_ms);

        $subjects_mse = [
            ['Professional Software Engineering', 'MSE800', $period=0, $teacherIndex=1, $credits=30],
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
        $subjects_data = [
            ['Wānanga Design Studio I: Whakapapa/Relationships', 'ARCH500', $period=3, $teacherIndex=3, $credits=30],
            ['Wānanga Design Studio II: Materials and Making', 'ARCH501', 3, 4, 30],
            ['Architectural Intelligence I: Anthropocene', 'ARCH502', 3, 5, 15],
            ['Architectural Communication', 'ARCH503', 3, 5, 15],
            ['Architectural Ecologies I: Material Assemblies', 'ARCH504', 3, 5, 15],//year1
            ['Wānanga Design Studio III: Mauri Ora I', 'ARCH600', 4, 3, 30],//year2
            ['Wānanga Design Studio IV: Mauri Ora II', 'ARCH601', 4, 4, 30],
            ['Architectural Intelligence II: The Pacific City', 'ARCH602', 4, 5, 30],
            ['Architectural Ecologies II: Medium-Scale Construction', 'ARCH604', 4, 4, 30],
        ];
        $subjects_aut = [];
        foreach ($subjects_data as $subjectData) {
            $entity = new Subject();
            $subjects_aut[] = $entity;
            $entity->setName($subjectData[0]);
            $entity->setCode($subjectData[1]);
            $entity->setCourse($course_a_ms);
            $this->entityManager->persist($entity);
        }

        //Intakes yoobee
        $intakes_data = [
            [
                'MSE 2311',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2023-11-13'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-11-08'),
                $course_y_mse
            ],
            [
                'MSE 2307',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2023-07-13'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-07-08'),
                $course_y_mse
            ],
            [//index 2
                'AK1337 2403',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-03-03'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2027-03-03'),
                $course_a_ba
            ],
            [
                'AK1337 2503',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-03'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2028-03-03'),
                $course_a_ba
            ],
        ];
        $intakes = [];
        foreach ($intakes_data as $intakeData) {
            $intake = new Intake();
            $intakes[] = $intake;
            $intake->setName($intakeData[0]);
            $intake->setStart($intakeData[1]);
            $intake->setFinish($intakeData[2]);
            $intake->setCourse($intakeData[3]);
            $this->entityManager->persist($intake);
        }

        //Periods yoobee
        $periods_data = [
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
            [//aut bachelor i3
                'Year 1',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2024-03-03'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-03'),
                $intakes[2],
            ],
            [//aut bachelor i4
                'Year 2',
                \DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-03'),
                \DateTimeImmutable::createFromFormat('Y-m-d', '2026-03-03'),
                $intakes[2],
            ],
        ];
        $periods = [];
        foreach ($periods_data as $periodData) {
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
        foreach ($subjects_data as $index => $subjectData) {
            $entity = new PeriodToSubject();
            $entity->setSubject($subjects_aut[$index]);
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
            $user->setIntake($intakes[rand(0,1)]);
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

        $studentsData = file_get_contents('/app/src/Command/students_aut');
        $studentsData = explode("\n", $studentsData);

        foreach ($studentsData as $studentItem) {
            $studentItem = explode(',', $studentItem);

            $user = new Student();
            $user->setIntake($intakes[2]);
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
