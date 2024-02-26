<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @implements PasswordUpgraderInterface<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void {
        if (!$user instanceof Student) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $ids
     * @return array<Student>
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findByIdList(array $ids): array
    {
        $b = $this->createQueryBuilder('a');
        return $b
            ->andWhere($b->expr()->in('a.id', $ids))
            ->indexBy('a', 'a.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $intakeId
     * @return array<Student>
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findByIntakeId(int $intakeId): array
    {
        $b = $this->createQueryBuilder('a');
        return $b
            ->andWhere($b->expr()->in('a.intake', $intakeId))
            ->indexBy('a', 'a.id')
            ->getQuery()
            ->getResult();
    }
}
