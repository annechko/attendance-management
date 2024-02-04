<?php

namespace App\Entity;

use App\Repository\PeriodToSubjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodToSubjectRepository::class)]
#[ORM\UniqueConstraint(fields: ['period', 'subject'])]
class PeriodToSubject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private int $totalNumberOfLessons = 0;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(fetch: 'EAGER',inversedBy: 'periodToSubjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Period $period = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalNumberOfLessons(): int
    {
        return $this->totalNumberOfLessons;
    }

    public function setTotalNumberOfLessons(int $totalNumberOfLessons): static
    {
        $this->totalNumberOfLessons = $totalNumberOfLessons;

        return $this;
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

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): static
    {
        $this->period = $period;

        return $this;
    }
}
