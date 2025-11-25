<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\OpcionMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpcionMenu>
 *
 * @method OpcionMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpcionMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpcionMenu[]    findAll()
 * @method OpcionMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpcionMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpcionMenu::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(OpcionMenu $entity, bool $flush = true): void
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
    public function remove(OpcionMenu $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return OpcionMenu[] Returns an array of OpcionMenu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OpcionMenu
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
