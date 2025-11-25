<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Enitity\FactMejoratmp;
use App\Enitity\FactMejora;
use App\Entity\Mantenimiento;
use Doctrine\Persistence\ManagerRegistry;

class json_lstFactMejora
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


    private $native = '';
    private $id_mant = '';

    public function setIdMant($id_mant)
    {
        $this->id_mant = $id_mant;
    }

    public function setNative(string $condi){
        $this->native = $condi;
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

        $idMant = $this->id_mant;
        if ($this->native=='yes')
        {
            $tableName = 'App\Entity\FactMejora';
        } else {
            $tableName = 'App\Entity\FactMejoratmp';
        }

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT det.id_det,det.fecha_fact,det.nro_fact,det.proveedor, det.proveedor_rif,
                        det.detalle, det.costo_fact, det.imp_fact,det.total_fact
                    FROM $tableName det
                    WHERE det.id_mant = '{$idMant}'
                    ";
    	} else {
		    	$sql = " 
                    SELECT det.id_det,det.fecha_fact,det.nro_fact,det.proveedor, det.proveedor_rif,
                        det.detalle, det.costo_fact, det.imp_fact,det.total_fact
                    FROM $tableName det
                    WHERE det.id_mant = '{$idMant}' AND {$condiBusca}
                    ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;
        $idMant =  $this->id_mant;
        if ($this->native=='yes')
        {
            $tableName = 'App\Entity\FactMejora';
        } else {
            $tableName = 'App\Entity\FactMejoratmp';
        }

        if (empty($txtSearch)){
		    	$sql = "
                    SELECT count(det)
                    FROM $tableName det
                    WHERE det.id_mant = '{$idMant}'
               ";
    	} else {
                $sql = "
                    SELECT count(det)
                    FROM $tableName det
                    WHERE det.id_mant = '{$idMant}' AND {$condiBusca}
                ";
    	}
    	return $sql;
	}

    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("det.id_mant","det.id_det","det.detalle","det.proveedor","det.proveedor_rif","det.nro_fact"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'det.id_det';
                break;
            case 1:
                $this->fieldOrder = 'det.nro_fact';
                break;
            case 2:
                $this->fieldOrder = 'det.proveedor';
                break;
            case 3:
                $this->fieldOrder = 'det.detalle';
                break;

            case 4:
                $this->fieldOrder = 'det.costo_fact';
                break;

            case 5:
                $this->fieldOrder = 'det.imp_fact';
                break;


            case 6:
                $this->fieldOrder = 'det.total_fact';
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
            $html_col1 = '<p>'.$fila['id_det'].'</p><input type="hidden" id="name_'.$fila['id_det'].'" value="'.$fila['detalle'].'" />';
            //$html_col1. = '<input type="hidden" id="status_'.$fila['id_traslado'].'" value="'.$fila['estatus'].'" />';
            $html_prov = '<p>'.$fila['proveedor'].'</p><p><b>Rif:'.$fila['proveedor_rif'].'</b></p>';

            $html_fact = '<p><b>'.$fila['nro_fact'].'</b> '.$fila['fecha_fact']->format("d/m/Y").'</p>';
            $nohay=false;

	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_det'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "fact"=>$html_fact,
                "proveedor"=>$html_prov,
                "detalle"=>$fila['detalle'],
                "costo"=>'<p class="text_right">'.number_format($fila['costo_fact'],2,'.',',').'</p>',
                "impuesto"=>'<p class="text_right">'.number_format($fila['imp_fact'],2,'.',',').'</p>',
                "total"=>'<p class="text_right">'.number_format($fila['total_fact'],2,'.',',').'</p>',
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','fact'=>'','proveedor'=>'','detalle'=>'','costo'=>'','impuesto'=>'','total'=>'');
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