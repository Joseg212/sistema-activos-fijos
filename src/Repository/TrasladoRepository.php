<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Traslado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Traslado>
 *
 * @method Traslado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traslado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traslado[]    findAll()
 * @method Traslado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrasladoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traslado::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Traslado $entity, bool $flush = true): void
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
    public function remove(Traslado $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Traslado[] Returns an array of Traslado objects
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
    public function findOneBySomeField($value): ?Traslado
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByGetOtherData($idCode):array
    {
        $dataOther = array();

        $em = $this->getEntityManager();

        $sql = "
            SELECT tra.id_ubic_orig as idUbicOrig, tra.id_ubic_dest as idUbicDest,
                tra.id_resp_emisor as idRespEmisor, tra.id_resp_destino as idRespDestino,
                CONCAT(res1.nombre,' ',res1.apellido) as nombreRespEmisor,
                res1.cargo as cargoRespEmisor, res1.telefono as telfRespEmisor,res1.movil as movilRespEmisor,
                CONCAT(res2.nombre,' ',res2.apellido) as nombreRespDestino,
                res2.cargo as cargoRespDestino, res2.telefono as telfRespDestino,res2.movil as movilRespDestino,
                p.nombre as nombrePropDestino, p.id_propiedad as idPropDestino
            FROM App\Entity\Traslado tra
            INNER JOIN App\Entity\Responsable res1 WITH res1.id_resp = tra.id_resp_emisor 
            INNER JOIN App\Entity\Responsable res2 WITH res2.id_resp = tra.id_resp_emisor 
            INNER JOIN App\Entity\Ubicacion ub WITH ub.id_ubic = tra.id_ubic_dest
            INNER JOIN App\Entity\Propiedad p   WITH p.id_propiedad = ub.id_propiedad
            WHERE tra.id_traslado='{$idCode}'
        ";

        $qry_data = $em->createQuery($sql);
        if ($qry_data)
        {
            $rstData = $qry_data->getSingleResult();

            $dataOther=[
                'idUbicOrig' => $rstData['idUbicOrig'],
                'idUbicDest' => $rstData['idUbicDest'],
                'idRespEmisor' => $rstData['idRespEmisor'],
                'idRespDestino' => $rstData['idRespDestino'],
                'nombreRespEmisor' => $rstData['nombreRespEmisor'],
                'cargoRespEmisor' => $rstData['cargoRespEmisor'],
                'telfRespEmisor' => $rstData['telfRespEmisor'],
                'movilRespEmisor' => $rstData['movilRespEmisor'],
                'nombreRespDestino' => $rstData['nombreRespDestino'],
                'cargoRespDestino' => $rstData['cargoRespDestino'],
                'telfRespDestino' => $rstData['telfRespDestino'],
                'movilRespDestino' => $rstData['movilRespDestino'],
                'nombrePropDestino' => $rstData['nombrePropDestino'],
                'idPropDestino'  => $rstData ['idPropDestino'],
            ];

        } else {
            $dataOther=[
                'idUbicOrig' => "",
                'idUbicDest' => "",
                'idRespEmisor' => "",
                'idRespDestino' => "",
                'nombreRespEmisor' => "",
                'cargoRespEmisor' => "",
                'telfRespEmisor' => "",
                'movilRespEmisor' => "",
                'nombreRespDestino' => "",
                'cargoRespDestino' => "",
                'telfRespDestino' => "",
                'movilRespDestino' => "",
                'idPropDestino' => "",
                'nombrePropDestino' => "",
            ];
        }
        return $dataOther;
    }
    public function findByTrasladoArray($idCode):array
    {
        $em = $this->getEntityManager();

        $data = array();

        $sql = "
            SELECT tra FROM App\Entity\Traslado tra WHERE tra.id_traslado='{$idCode}'
        ";


        $qry_tras  = $em->createQuery($sql);
        if ($qry_tras){
            $rst_tras = $qry_tras->getSingleResult();

            $data['id_traslado'] = $rst_tras->getIdTraslado();
            $data['id_af'] = $rst_tras->getIdAf();
            $data['tipo_traslado'] = $rst_tras->getTipoTraslado();
            $data['fecha_traslado'] = $rst_tras->getFechaTraslado()->format("d/m/Y");
            $data['id_resp_emisor'] = $rst_tras->getIdRespEmisor();
            $data['id_resp_destino'] = $rst_tras->getIdRespDestino();
            $data['id_ubic_orig'] = $rst_tras->getIdUbicOrig();
            $data['id_ubic_dest'] = $rst_tras->getIdUbicDest();
            $data['destino_externo_ubic'] = $rst_tras->getDestinoExternoUbic();
            $data['destino_externo_info'] = $rst_tras->getDestinoExternoInfo();
            $data['observ'] = $rst_tras->getObserv();
            $data['motivo'] = $rst_tras->getMotivo();
            $data['tipoDes'] = $rst_tras->getTipoDes();
            $data['estatus'] = $rst_tras->getEstatus();

        }

        return $data;
    }
}
