<?php

namespace App\Repository;

use App\Entity\Mantenimiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\TryCatch;

/**
 * @extends ServiceEntityRepository<Mantenimiento>
 *
 * @method Mantenimiento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mantenimiento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mantenimiento[]    findAll()
 * @method Mantenimiento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MantenimientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mantenimiento::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Mantenimiento $entity, bool $flush = true): void
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
    public function remove(Mantenimiento $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Mantenimiento[] Returns an array of Mantenimiento objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mantenimiento
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByMejoraActivo($idAF):array
    {
        $mejora = array();

        $em = $this->getEntityManager();

        $sql = "SELECT mant.id_mant,mant.monto_fact, mant.monto_iva,mant.total_factura,
                mant.costo_traslado,mant.imp_traslado,mant.total_traslado
                FROM App\Entity\Mantenimiento mant
                WHERE mant.id_af='{$idAF}' AND mant.nro_fact=''
                ";
        $qry_mejora = $em->createQuery($sql);
        
        try {
            $rst_mejora = $qry_mejora->getSingleResult();
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            $rst_mejora=false;
        }

        if ($rst_mejora){
            $mejora = [
                'id_mant'   =>$rst_mejora['id_mant'],
                'costo_mej' =>number_format($rst_mejora['monto_fact'],2,'.',','),
                'imp_mej'   => number_format($rst_mejora['monto_iva'],2,'.',','),
                'total_mej' =>number_format($rst_mejora['total_factura'],2,'.',','),
                'costo_flete'=>number_format($rst_mejora['costo_traslado'],2,'.',','),
                'imp_flete' =>number_format($rst_mejora['imp_traslado'],2,'.',','),
                'total_flete'=>number_format($rst_mejora['total_traslado'],2,'.',','),
            ];

        } else {
            $mejora = [
                'id_mant'=>'',
                'costo_mej'=>'0.00',
                'imp_mej'=>'0.00',
                'total_mej'=>'0.00',
                'costo_flete'=>'0.00',
                'imp_flete'=>'0.00',
                'total_flete'=>'0.00',
            ];
        }

        return $mejora;
    }
    public function findByGastosActivo(string $idActivo):array
    {
        $data = ["estado"=>"not"];

        $em = $this->getEntityManager();

        $sql = "SELECT u.id_mant, u.tipo_mant, u.fecha_fact, u.monto_fact, u.monto_iva,
            u.total_factura,u.detalle,u.si_traslado, u.costo_traslado, u.imp_traslado,
            u.total_traslado
        FROM App\Entity\Mantenimiento u 
        WHERE u.id_af='{$idActivo}'
        ORDER BY u.id_mant desc
        ";

        $qry_amortiz = $em->createQuery($sql);

        try {
            $result = $qry_amortiz->getResult();

            $data['gastos']=array();

            foreach($result as $row){
                $data['gastos'][] = array(
                    'id'                =>   $row['id_mant'],
                    'tipo'              =>   $row['tipo_mant'],
                    'fecha'             =>   $row['fecha_fact']->format("d/m/Y"),
                    'costo_fact'        =>   number_format($row['monto_fact'],2,'.',','),
                    'imp_fact'          =>   number_format($row['monto_iva'],2,'.',','),
                    'total_fact'        =>   number_format($row['total_factura'],2,'.',','),
                    'costo_tras'        =>   number_format($row['costo_traslado'],2,'.',','),
                    'imp_tras'          =>   number_format($row['imp_traslado'],2,'.',','),
                    'total_tras'        =>   number_format($row['total_traslado'],2,'.',',')
                );
            }
            $data['estado']="yes";
        } catch (\Exception $err) {
            //throw $th;
            $data['message']  =  $err->getMessage() . " " . $err->getLine();
        }

        return $data;
    }    
    public function findByGastosRango(\DateTimeInterface $fechaDesde,\DateTimeInterface $fechaHasta, string $idPropiedad) :array
    {
        $data = ["estado"=>"not"];

        $em = $this->getEntityManager();

        $inicial = $fechaDesde->format("Y/m/d");
        $final = $fechaHasta->format("Y/m/d");

        $sql = "SELECT mant.id_mant,mant.fecha_fact,mant.tipo_mant,mant.nro_fact,mant.proveedor, mant.proveedor_rif,
                    mant.banco,mant.tipo_doc,mant.numero_doc,mant.monto_fact,mant.monto_iva,mant.total_factura,
                    mant.unidad_tiempo,mant.numero_tiempo,mant.detalle,mant.si_traslado,mant.costo_traslado,
                    mant.imp_traslado,mant.total_traslado,cla.id_clase,
                    act.descrip,act.fecha_compra,cla.descripcion as clasific, act.id_af
            FROM App\Entity\Mantenimiento mant
            INNER JOIN App\Entity\Activofijo act WITH act.id_af = mant.id_af 
            INNER JOIN App\Entity\Clasificacion cla WITH cla.id_clase = act.id_clase
            INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = act.id_ubic
            INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
            WHERE mant.fecha_fact>='{$inicial}' AND  mant.fecha_fact<='{$final}' AND pro.id_propiedad = '{$idPropiedad}'
            ORDER BY cla.descripcion,mant.id_af,mant.fecha_fact
        ";

        $qry_amortiz = $em->createQuery($sql);

        try {
            $result = $qry_amortiz->getResult();

            $data['calculos']=array();

            $contar = 0;
            $x=-1;
            $token = 1;
            $posc = 0;
            foreach($result as $row){
                $contar++;
                    
                $data['calculos'][] = array(
                    'id_mant'          =>   $row['id_mant'],
                    'id_af'            =>   $row['id_af'],
                    'id_clase'         =>   $row['id_clase'],
                    'descrip'          =>   $row['descrip'],
                    'clasific'         =>   $row['clasific'],
                    'tipo_mant'        =>   $row['tipo_mant'],
                    'fecha_fact'       =>   $row['fecha_fact'],
                    'numero_fact'      =>   $row['nro_fact'],
                    'proveedor'        =>   $row['proveedor'],
                    'prov_rif'         =>   $row['proveedor_rif'],
                    'banco'            =>   $row['banco'],
                    'tipo_doc'         =>   $row['tipo_doc'],
                    'numero_doc'       =>   $row['numero_doc'],
                    'costo_fact'       =>   $row['monto_fact'],
                    'imp_fact'         =>   $row['monto_iva'],
                    'total_fact'       =>   $row['total_factura'],
                    'traslado'         =>   [
                                                'si'=>$row['si_traslado'],
                                                'costo_tras'=>$row['costo_traslado'],
                                                'imp_tras'=>$row['imp_traslado'],
                                                'total_tras'=>$row['total_traslado'],
                                            ],
                    'unidad_tiempo'    =>   $row['unidad_tiempo'],
                    'numero_tiempo'    =>   $row['numero_tiempo'],
                    'detalle'          =>   $row['detalle'],
                );
                $contar=0;

            }
            $data['estado']="yes";
        } catch (\Exception $err) {
            //throw $th;
            $data['message']  =  $err->getMessage() . " " . $err->getLine();
        }

        return $data;
    }    

}
