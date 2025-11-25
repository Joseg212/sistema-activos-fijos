<?php

namespace App\Repository;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Amortizaciones;
use App\Entity\TipoAmortiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;

/**
 * @extends ServiceEntityRepository<Amortizaciones>
 *
 * @method Amortizaciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Amortizaciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Amortizaciones[]    findAll()
 * @method Amortizaciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmortizacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amortizaciones::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Amortizaciones $entity, bool $flush = true): void
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
    public function remove(Amortizaciones $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Amortizaciones[] Returns an array of Amortizaciones objects
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
    public function findOneBySomeField($value): ?Amortizaciones
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByVerificarAmortiz(string $idAf)
    {
        $em = $this->getEntityManager();

        $sql = "
        SELECT a.id_af, count(a.id_af) as total,
               MAX(concat(a.anio,'-',a.mes)) as tiempo,
               ((tp.tiempo_estimado*12)+1) as tiempo_est
         FROM App\Entity\Amortizaciones a
        INNER JOIN App\Entity\TipoAmortiz tp WITH tp.id_af = a.id_af
        WHERE a.id_af='{$idAf}'
        GROUP BY a.id_af 
        ORDER BY a.id_af, a.mes, a.anio        
        ";

        $qry_amortiz = $em->createQuery($sql);


        return $qry_amortiz->getOneOrNullResult();
    } 
    public function findByObtenerMaximoId($idAf):string
    {
        $idMax="";
        $em = $this->getEntityManager();

        $sql = "
            SELECT max(a.id_amortiz) as maximo 
            FROM App\Entity\Amortizaciones a 
            WHERE a.id_af='{$idAf}'
            GROUP BY a.id_af
        ";

        $qry_max = $em->createQuery($sql);

        try {
            $result = $qry_max->getSingleResult();
            $idMax = $result['maximo'];
        } catch (\Throwable $th) {
            $idMax="";
        }

        return $idMax;
    }
    public function findByAmortizaciones(string $idActivo):array
    {
        $data = ["estado"=>"not"];

        $em = $this->getEntityManager();

        $sql = "SELECT u.mes,u.anio,u.periodo,u.residual,u.factor_correc as factor, u.revalorizado as nuevo, 
                u.amortiz_calc, u.amortiz_acum, u.costo_activo, u.factor_original,u.relacion_porc
        FROM App\Entity\Amortizaciones u 
        WHERE u.id_af='{$idActivo}'
        ORDER BY u.periodo,u.anio,u.mes
        ";

        $qry_amortiz = $em->createQuery($sql);

        try {
            $result = $qry_amortiz->getResult();

            $data['calculos']=array();

            $contar = 0;
            foreach($result as $row){
                $contar++;
                if ($contar==12 || $row['periodo']=="0"){
                    $data['calculos'][] = array(
                        'mes'              =>   $row['mes'],
                        'anio'             =>   $row['anio'],
                        'periodo'          =>   $row['periodo'],
                        'residual'         =>   number_format($row['residual'],2,'.',','),
                        'factor'           =>   number_format($row['factor'],6,'.',','),
                        'nuevo'            =>   number_format($row['nuevo'],2,'.',','),
                        'amortiz_calc'     =>   number_format($row['amortiz_calc'],2,'.',','),
                        'amortiz_acum'     =>   number_format($row['amortiz_acum'],2,'.',','),
                        'costo_activo'     =>   number_format($row['costo_activo'],2,'.',','),
                        'factorOrig'       =>   number_format($row['factor_original'],6,'.',','),
                        'relacionPorc'     =>   number_format($row['relacion_porc'],2,'.',','), 
                    );
                    $contar=0;
                }
            }
            $data['estado']="yes";
        } catch (\Exception $err) {
            //throw $th;
            $data['message']  =  $err->getMessage() . " " . $err->getLine();
        }

        return $data;
    }    
    public function findByAmortizRango(\DateTimeInterface $fechaDesde,\DateTimeInterface $fechaHasta, string $idPropiedad) :array
    {
        $data = ["estado"=>"not"];

        $em = $this->getEntityManager();

        $inicial = $fechaDesde->format("Ym");
        $final = $fechaHasta->format("Ym");

        $sql = "SELECT cla.id_clase,u.mes,u.anio,u.periodo,u.residual,u.factor_correc as factor, u.revalorizado as nuevo, 
                u.amortiz_calc, u.amortiz_acum, u.costo_activo, u.factor_original,u.relacion_porc,
                act.descrip,act.fecha_compra,cla.descripcion as clasific, act.id_af, u.costo_hist
        FROM App\Entity\Amortizaciones u
        INNER JOIN App\Entity\Activofijo act WITH act.id_af = u.id_af 
        INNER JOIN App\Entity\Clasificacion cla WITH cla.id_clase = act.id_clase
        INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = act.id_ubic
        INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
        WHERE concat(u.anio,u.mes)>='{$inicial}' AND  concat(u.anio,u.mes)<='{$final}' AND pro.id_propiedad = '{$idPropiedad}'
        ORDER BY cla.descripcion,u.id_af,u.anio,u.mes
        ";

        $qry_amortiz = $em->createQuery($sql);

        try {
            $result = $qry_amortiz->getResult();

            $data['calculos']=array();

            $contar = 0;
            $x=-1;
            $token = 1;
            $posc = 0;
            $sihay = false;
            foreach($result as $row){
                $sihay = true;
                $contar++;
                if ($row['periodo']!="0"){
                    
                    $x++;
                    if ($token==1){
                        $posc = $x;
                        $token=0;
                    }
                    $data['calculos'][] = array(
                        'id_af'            =>   $row['id_af'],
                        'id_clase'         =>   $row['id_clase'],
                        'descrip'          =>   $row['descrip'],
                        'fecha'            =>   $row['fecha_compra'],
                        'clasific'         =>   $row['clasific'],
                        'mes'              =>   $row['mes'],
                        'anio'             =>   $row['anio'],
                        'periodo'          =>   $row['periodo'],
                        'residual'         =>   $row['residual'],
                        'factor'           =>   $row['factor'],
                        'nuevo'            =>   $row['nuevo'],
                        'costo_hist'       =>   $row['costo_hist'],
                        'amortiz_calc'     =>   $row['amortiz_calc'],
                        'amortiz_acum'     =>   $row['amortiz_acum'],
                        'amortiz_ajust'    =>   0.00,
                        'costo_activo'     =>   $row['costo_activo'],
                        'factorOrig'       =>   $row['factor_original'],
                        'relacionPorc'     =>   $row['relacion_porc'],
                    );
                    $contar=0;
                }
                if ($row['anio'].$row['mes']==$final)
                {
                    $token=1;
                    $data['calculos'][$posc]['amortiz_ajust'] = $row['amortiz_acum'];
                }
            }
            if ($sihay==true){
                $data['estado']="yes";
            } else {
                $data['estado']="not";
            }
        } catch (\Exception $err) {
            //throw $th;
            $data['message']  =  $err->getMessage() . " " . $err->getLine();
        }

        return $data;
    }    
}
