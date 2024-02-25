<?php

namespace App\Entity;

use App\Repository\AttendanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
class Attendance
{
    public const STATUS_PRESENT = 1;
    public const STATUS_ABSENT = 2;
    public const STATUS_EXCUSED = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function getSubjectId(): ?int
    {
        return $this->subject?->getId();
    }

    public function getStudentId(): ?int
    {
        return $this->student?->getId();
    }

    public function getStudentName(): string
    {
        return $this->student->getName() . ' ' . $this->student->getSurname();
    }

    public function getTeacherId(): ?int
    {
        return $this->teacher?->getId();
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setDateValue(string $value): void
    {
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
    }

    public function getDateValue(): string
    {
        if ($this->getDate()) {
            return $this->getDate()->format('Y-m-d');
        }
        return '';
    }
}
