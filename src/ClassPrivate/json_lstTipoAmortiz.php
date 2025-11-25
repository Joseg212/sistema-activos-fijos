<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Entity\Clasificacion;
use Doctrine\Persistence\ManagerRegistry;

class json_lstTipoAmortiz
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

    private $idPropiedad;

    public function setIdPropiedad($idPropiedad)
    {
        $this->idPropiedad = $idPropiedad;
    }

    private $idUbicacion;

    public function setIdUbicacion($idUbicacion)
    {
        $this->idUbicacion = $idUbicacion;
    }

    private $funcGlobal=null;

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
        $idUbicacion = $this->idUbicacion;

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.code_activof,p.costo_total,
                    COALESCE(d.formula,'S/Definir') AS formula,
                    COALESCE(d.valor_salvamento,0000000000.00) AS valors,
                    COALESCE(d.id_tipom,'NS100001') AS id_tipom
                    FROM App\Entity\Activofijo p
                    LEFT JOIN App\Entity\TipoAmortiz d WITH d.id_af = p.id_af
                    WHERE p.estatus='activo' AND p.id_ubic = '$idUbicacion'
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.code_activof,p.costo_total,
                    COALESCE(d.formula,'S/Definir') AS formula,
                    COALESCE(d.valor_salvamento,0000000000.00) AS valors
                    COALESCE(d.id_tipom,'NS100001') AS id_tipom
                    FROM App\Entity\Activofijo p
                    LEFT JOIN App\Entity\TipoAmortiz d WITH d.id_af = p.id_af
                    WHERE p.estatus='activo' AND p.id_ubic = '$idUbicacion' AND $condiBusca
	                ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;
        $idUbicacion = $this->idUbicacion;

		if (empty($txtSearch)){
		    	$sql = "SELECT count(p.id_ubic)
                        FROM App\Entity\Activofijo p 
                        WHERE p.estatus='activo' AND p.id_ubic = '$idUbicacion'
                       ";
    	} else {
                $sql = "SELECT count(p.id_ubic)
                        FROM App\Entity\Activofijo p 
                        WHERE p.estatus='activo' AND p.id_ubic = '$idUbicacion' AND $condiBusca
                    ";
    	}
    	return $sql;
	}


    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("p.id_ubic,p.id_clase,p.num_serie,p.distribuidor,p.code_activof,p.descrip,p.nrofact,p.edo_fisico"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'p.id_af';
                break;
            case 1:
                $this->fieldOrder = 'p.descrip';
                break;
            case 2:
                $this->fieldOrder = 'p.fecha_compra';
                break;

            case 3:
                $this->fieldOrder = 'p.code_activof';
                break;

            case 4:
                $this->fieldOrder = 'p.costo_total';
                break;

            case 5:
                $this->fieldOrder = 'd.formula';
                break;

            case 6:
                $this->fieldOrder = 'd.valor_salvamento';
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
        $html_col2="";

	    foreach ($resultArray as $fila) {
            $html_col1 = '<p>'.$fila['id_af'].'</p><input type="hidden" id="name_'.$fila['id_af'].'" value="'.$fila['descrip'].'" />';
            $html_col2 = '<p>'.$fila['descrip'].'</p><input type="hidden" id="code_tipo_'.$fila['id_af'].'" value="'.$fila['id_tipom'].'" />';
            $nohay=false;
	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_af'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "descrip"=>$html_col2,
                "fechac"=>$fila['fecha_compra']->format("d/m/Y"),
                "code"=>$fila['code_activof'],
                "costot"=> '<div class="number-right-bold-af">'.number_format($fila['costo_total'],2,'.',',').'</div>',
                "formula"=> '<div class="text-completed-center-af"><label>'.$fila['formula'].'</label></div>',
                "valors"=> '<div class="number-right-normal-af">'.number_format($fila['valors'],2,".",",").'</div>',
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','descrip'=>'','fechac'=>'','code'=>'','costot'=>'','formula'=>'','valors'=>'');
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