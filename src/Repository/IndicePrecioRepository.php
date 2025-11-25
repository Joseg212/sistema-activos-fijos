<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\IndicePrecio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<IndicePrecio>
 *
 * @method IndicePrecio|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndicePrecio|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndicePrecio[]    findAll()
 * @method IndicePrecio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicePrecioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndicePrecio::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(IndicePrecio $entity, bool $flush = true): void
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
    public function remove(IndicePrecio $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return IndicePrecio[] Returns an array of IndicePrecio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IndicePrecio
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByFactorDeCorrecion(string $mesInicial, string $anioInicial,string $mesFinal, string $anioFinal):array
    {

        $factor = array("factor"=>0.00, "reconver"=>0.00);

        $indiceInicial = 0.00;
        $indiceFinal = 0.00;
        $reconver = 0.00;

        $em = $this->getEntityManager();

        $sql = "
        SELECT u.factor, u.reconver, u.mes, u.anio
        FROM App\Entity\IndicePrecio u 
        WHERE ((u.mes='{$mesInicial}' AND u.anio='{$anioInicial}') OR (u.mes='{$mesFinal}' AND  u.anio='{$anioFinal}')) AND u.grupo=1
        ORDER BY u.anio,u.mes,u.factor
            ";

        $qry_indp = $em->createQuery($sql);

        $rst_indp = $qry_indp->getResult();

        if ($rst_indp){
            foreach ($rst_indp As $fila)
            {
                if ($fila['mes']==$mesInicial && $fila['anio']==$anioInicial)
                {
                    $indiceInicial=$fila['factor'];
                }
                if ($fila['mes']==$mesFinal && $fila['anio']==$anioFinal)
                {
                    $indiceFinal=$fila['factor'];
                    $reconver = $fila['reconver'];
                }
            }
            // se calcula el factor de corelacion 
            $factor["factor"] = round($indiceFinal/$indiceInicial,6);
            
            if ($reconver>0){
                $factor["reconver"] = $reconver;
            }
        }
        return $factor;
    }
    public function findByReconver(string $desde, string $hasta):float
    {
        $em = $this->getEntityManager();
        $reconver = 0.00;
        try {
            $sql ="
                SELECT u.reconver
                FROM App\Entity\IndicePrecio u
                WHERE u.reconver>0 and 
                CONCAT(u.anio,u.mes)>={$desde} and 
                CONCAT(u.anio,u.mes)<={$hasta}
            ";

            $qry_reconverLst  = $em->createQuery($sql);

            $rst_reconverLst = $qry_reconverLst->getResult();
            foreach($rst_reconverLst As $fila)
            {
                if ($reconver==0){
                    $reconver=$fila['reconver'];
                } else {
                    $reconver*=$fila['reconver'];
                }
            }

            //code...
        } catch (\Exception $err) {
            //throw $th;
        }
        return $reconver;
    }
}
