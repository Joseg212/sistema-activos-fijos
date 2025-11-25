<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Activofijo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activofijo>
 *
 * @method Activofijo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activofijo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activofijo[]    findAll()
 * @method Activofijo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivofijoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activofijo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Activofijo $entity, bool $flush = true): void
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
    public function remove(Activofijo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Activofijo[] Returns an array of Activofijo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activofijo
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOneByDataActivo($idActivo):array
    {
        $arrActivo = array();

        $em = $this->getEntityManager();

        $sql = "
            SELECT act.id_af,act.descrip, act.num_serie,
                act.fecha_compra,act.edo_fisico,
                act.nrofact,act, act.distribuidor as dist, act.rif as dist_rif,
                act.costo  as costo_act, act.impuesto as imp_fact, act.costo_total as total_act,
                act.costo_flete,act.estatus,act.id_clase, cla.descripcion As clasific,
                ub.id_ubic, ub.ubicacion, p.id_propiedad,p.nombre 
            FROM App\Entity\Activofijo act
            INNER JOIN App\Entity\Clasificacion cla WITH cla.id_clase = act.id_clase
            INNER JOIN App\Entity\Ubicacion ub WITH ub.id_ubic = act.id_ubic
            INNER JOIN App\Entity\Propiedad p WITH p.id_propiedad = ub.id_propiedad
            WHERE act.id_af='{$idActivo}'
        ";

        $query = $em->createQuery($sql);

        if ($query)
        {
            $activoFijo = $query->getSingleResult();

            $arrActivo = array(
                'idAf'          =>$activoFijo['id_af'],
                'descrip'       =>$activoFijo['descrip'],
                'fechac'        =>$activoFijo['fecha_compra']->format("d/m/Y"),
                'edofisico'     =>$activoFijo['edo_fisico'],
                'num_serie'     =>$activoFijo['num_serie'],
                'idUbic'        =>$activoFijo['id_ubic'],
                'ubicacion'     =>$activoFijo['ubicacion'],
                'idProp'        =>$activoFijo['id_propiedad'],
                'propiedad'     =>$activoFijo['nombre'],
                'nrofact'       =>$activoFijo['nrofact'],
                'dist'          =>$activoFijo['dist'],
                'dist_rif'      =>$activoFijo['dist_rif'],
                'costo_act'     =>number_format($activoFijo['costo_act'],2,'.',','),
                'imp_act'       =>number_format($activoFijo['imp_fact'],2,'.',','),
                'total_act'     =>number_format($activoFijo['total_act'],2,'.',','),
                'costo_flete'   =>number_format($activoFijo['costo_flete'],2,'.',','),
                'estatus'       =>$activoFijo['estatus'],
                'id_clase'      =>$activoFijo['id_clase'],
                'clasific'      =>$activoFijo['clasific'],
            ); 
        } else {
            $arrActivo = array(
                'idAf'          =>"",
                'descrip'       =>"",
                'fechac'        =>"",
                'edofisico'     =>"",
                'num_serie'     =>"",
                'idUbic'        =>"",
                'ubicacion'     =>"",
                'idProp'        =>"",
                'propiedad'     =>"",
                'nrofact'       =>"",
                'dist'          =>"",
                'dist_rif'      =>"",
                'costo_act'     =>"",
                'imp_act'       =>"",
                'total_act'     =>"",
                'costo_flete'   =>"",
                'estatus'       =>"",
            ); 
        }
        return $arrActivo;
    }
}
