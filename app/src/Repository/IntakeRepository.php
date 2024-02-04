<?php

namespace App\Repository;

use App\Entity\Intake;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intake>
 *
 * @method Intake|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intake|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intake[]    findAll()
 * @method Intake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntakeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intake::class);
    }
}
