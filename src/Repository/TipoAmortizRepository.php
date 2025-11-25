<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\TipoAmortiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoAmortiz>
 *
 * @method TipoAmortiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoAmortiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoAmortiz[]    findAll()
 * @method TipoAmortiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoAmortizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoAmortiz::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TipoAmortiz $entity, bool $flush = true): void
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
    public function remove(TipoAmortiz $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TipoAmortiz[] Returns an array of TipoAmortiz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipoAmortiz
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByTipoAmortiz(string $idActivo):array
    {
        $data = ["estado"=>"not"];

        $em = $this->getEntityManager();

        $sql = "SELECT u.formula, u.tiempo_estimado, u.valor_salvamento, u.observ FROM App\Entity\TipoAmortiz u WHERE u.id_af='{$idActivo}'";

        $qry_amortiz = $em->createQuery($sql);

        try {
            $row = $qry_amortiz->getSingleResult();
            $data['formula']    = $row['formula'];
            $data['tiempo']     = $row['tiempo_estimado'];
            $data['valors']     = number_format($row['valor_salvamento'],2,'.',',');
            $data['observ']     = $row['observ'];
            $data['estado']     = "yes";
        } catch (\Exception $err) {
            //throw $th;
            $data['message']    = $err->getMessage() . " " . $err->getLine();
        }

        return $data;
    }
}
