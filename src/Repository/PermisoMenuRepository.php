<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\PermisoMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PermisoMenu>
 *
 * @method PermisoMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method PermisoMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method PermisoMenu[]    findAll()
 * @method PermisoMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermisoMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermisoMenu::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PermisoMenu $entity, bool $flush = true): void
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
    public function remove(PermisoMenu $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PermisoMenu[] Returns an array of PermisoMenu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PermisoMenu
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /* Devolver el array de l Menu */
    public function findByMenuOption($idUsuario):array
    {
        $arrMenu = array();

        $em = $this->getEntityManager();
            

        $sql = "
            SELECT om.nivel, om.orden, om.opcion, om.grupo,
            om.ruta, om.icono
            from App\Entity\OpcionMenu om 
            INNER JOIN App\Entity\PermisoMenu pm WITH pm.id_opcion = om.id_opcion AND pm.permiso='Activo'
            INNER JOIN App\Entity\Usuario us WITH us.id_us = pm.id_us 
            WHERE om.estatus='A' AND us.email = '{$idUsuario}'
            ORDER BY om.orden            
        ";


        $query = $em->createQuery($sql);

        $arrMenu = array();

        if ($query){
            $result = $query->getResult();
            foreach($result as $row){
                $arrMenu[] = array(
                    "nivel" => $row['nivel'],
                    "opcion" => $row['opcion'],
                    "grupo" => $row['grupo'],
                    "ruta" => $row['ruta'],
                    "icono" => $row['icono'],

                );

            }
        }

        return $arrMenu;
    }

}
