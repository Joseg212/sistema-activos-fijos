<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Entity\Mantenimiento;
use Doctrine\Persistence\ManagerRegistry;

class json_lstMejora
{

	private $searchDatos='';
	private $pageLong='';
	private $pageStart='';
    private $arrOrder = array();
    private $typeOrder = '';
    private $fieldOrder = '';
    private $rootWeb = '';
    private $txtSearch='';
    private $typeSearch='';

    private $funcGlobal=null;

    private $idActivo = '';

    public function setIdActivo($idActivo)
    {
        $this->idActivo = $idActivo;
    }

    public function rootWebSet($valor)
    {
        $this->rootWeb = $valor;
    }

    public function dataSearch($typeSearch, $txtSearch)
    {
        $this->txtSearch=$txtSearch;
        $this->typeSearch=$typeSearch;
    }

    public function setFuncGlobal($funcGlobal)
    {
        $this->funcGlobal = $funcGlobal;
        return;
    }


	private function queryFilter()
	{

		$condiBusca = $this->searchDatos;
        $txtSearch = $this->txtSearch;
        $typeOrder = $this->typeOrder;
        $fieldOrder = $this->fieldOrder;

        $idActivo = $this->idActivo;

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT mant.id_mant, act.descrip, mant.fecha_fact,
                        mant.detalle, mant.unidad_tiempo,mant.numero_tiempo,
                        mant.proveedor, mant.nro_fact, mant.total_factura, 
                        mant.monto_iva, mant.monto_fact
                    FROM App\Entity\Mantenimiento mant
                    INNER JOIN App\Entity\Activofijo act WITH mant.id_af=act.id_af AND act.estatus='activo'
                    WHERE mant.tipo_mant = 'Mejora' AND mant.id_af = '{$idActivo}'
                    ORDER BY mant.fecha_fact desc
                    ";
    	} else {
		    	$sql = " 
                    SELECT mant.id_mant, act.descrip, mant.fecha_fact,
                        mant.detalle, mant.unidad_tiempo,mant.numero_tiempo,
                        mant.proveedor, mant.nro_fact, mant.total_factura
                        mant.monto_iva, mant.monto_fact
                    FROM App\Entity\Mantenimiento mant
                    INNER JOIN App\Entity\Activofijo act WITH mant.id_af=act.id_af AND act.estatus='activo'
                    WHERE mant.tipo_mant = 'Mejora' AND mant.id_af = '{$idActivo}' AND {$condiBusca}
                    ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;
        $idActivo =  $this->idActivo;
		if (empty($txtSearch)){
		    	$sql = "
                SELECT count(mant.id_mant)
                FROM App\Entity\Mantenimiento mant
                INNER JOIN App\Entity\Activofijo act WITH mant.id_af=act.id_af AND act.estatus='activo'
                WHERE mant.tipo_mant = 'Mejora' AND mant.id_af = '{$idActivo}'
               ";
    	} else {
                $sql = "
                SELECT count(mant.id_mant)
                FROM App\Entity\Mantenimiento mant
                INNER JOIN App\Entity\Activofijo act WITH mant.id_af=act.id_af AND act.estatus='activo'
                WHERE mant.tipo_mant = 'Mejora' AND mant.id_af = '{$idActivo}' AND {$condiBusca}
                ";
    	}
    	return $sql;
	}

    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("mant.id_mant","mant.fecha_factura","mant.nro_fact","mant.detalle","mant.proveedor","act.descrip"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'mant.id_mant';
                break;
            case 1:
                $this->fieldOrder = 'actv.descrip';
                break;
            case 2:
                $this->fieldOrder = 'mant.fecha_fact';
                break;
            case 3:
                $this->fieldOrder = 'mant.detalle';
                break;

            case 4:
                $this->fieldOrder = 'mant.costo_fact';
                break;

            case 5:
                $this->fieldOrder = 'mant.imp_fact';
                break;


            case 6:
                $this->fieldOrder = 'mant.total_factura';
                break;

            }

    	/* Numero de registros */
        $dsql = $this->numberRegisters();
    	
	    $queryNumberRegisters = $docTrine->createQuery($dsql);

        $NumberRegisters=$queryNumberRegisters->getSingleScalarResult();

    	/*Carga de la Data */
        $dsql = $this->queryFilter();

    	
	    $querys = $docTrine->createQuery($dsql)->setMaxResults($this->pageLong)
        ->setFirstResult($this->pageStart);

	    $resultArray = $querys->getResult();
        $nohay=true;
        $rutaWeb = $this->rootWeb;
        $html_col1="";
        $colorType = "";
	    foreach ($resultArray as $fila) {
            $html_col1 = '<p>'.$fila['id_mant'].'</p><input type="hidden" id="name_'.$fila['id_mant'].'" value="'.$fila['descrip'].'" />';
            //$html_col1. = '<input type="hidden" id="status_'.$fila['id_traslado'].'" value="'.$fila['estatus'].'" />';
            $html_prov = '<p>'.$fila['proveedor'].'</p><p><b>Factura Nro.:'.$fila['nro_fact'].'</b></p>';

            $html_tiempo = '<p><b>'.$fila['numero_tiempo'].'</b> '.$fila['unidad_tiempo'].'</p>';
            $nohay=false;

	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_mant'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "activo"=>$fila['descrip'],
                "fecha"=>$fila['fecha_fact']->format("d/m/Y"),
                "detalle"=>$fila['detalle'],
                "costo"=>'<p class="text_right">'.number_format($fila['monto_fact'],2,'.',',').'</p>',
                "imp"=>'<p class="text_right">'.number_format($fila['monto_iva'],2,'.',',').'</p>',
                "total"=>'<p class="text_right">'.number_format($fila['total_factura'],2,'.',',').'</p>',
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','activo'=>'','fecha'=>'','detalle'=>'','costo'=>'','imp'=>'','total'=>'');
        }

		$data['recordsTotal']=$NumberRegisters;
		$data['recordsFiltered']=$NumberRegisters;

		return $data;
    }

    public function __construct($searchDatos,$pageLong,$pageStart,$arrOrder)
    {
    	$this->searchDatos	= $searchDatos;
    	$this->pageLong		= $pageLong;
    	$this->pageStart 	= $pageStart;
        $this->arrOrder     = $arrOrder;
    }
}

?>