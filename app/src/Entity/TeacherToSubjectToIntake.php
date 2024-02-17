<?php

namespace App\Entity;

use App\Repository\TeacherToSubjectToIntakeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherToSubjectToIntakeRepository::class)]
#[ORM\UniqueConstraint(fields: ['teacher', 'subject', 'intake'])]
class TeacherToSubjectToIntake
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $start = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $finish = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'teacherToSubjectToIntake')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intake $intake = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getIntake(): ?Intake
    {
        return $this->intake;
    }

    public function setIntake(?Intake $intake): void
    {
        $this->intake = $intake;
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(?\DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    public function getFinish(): ?\DateTimeImmutable
    {
        return $this->finish;
    }

    public function setFinish(?\DateTimeImmutable $finish): void
    {
        $this->finish = $finish;
    }

    public function isValid(): bool
    {
        if ($this->subject->getCourse()->getId() !== $this->intake->getCourse()->getId()) {
            return false;
        }
        return true;
    }

}
