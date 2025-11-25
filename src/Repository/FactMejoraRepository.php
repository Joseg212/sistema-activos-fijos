<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\FactMejora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FactMejora>
 *
 * @method FactMejora|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactMejora|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactMejora[]    findAll()
 * @method FactMejora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactMejoraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactMejora::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(FactMejora $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(FactMejora $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return FactMejora[] Returns an array of FactMejora objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FactMejora
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
