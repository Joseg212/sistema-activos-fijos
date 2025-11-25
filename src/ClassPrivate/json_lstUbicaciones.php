<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Ubicacion;
use Doctrine\Persistence\ManagerRegistry;

class json_lstUbicaciones
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
        $idPropiedad = $this->idPropiedad;

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT p.id_ubic,p.ubicacion,p.nota
                    FROM App\Entity\Ubicacion p
                    WHERE p.id_propiedad = '$idPropiedad'
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_ubic,p.ubicacion,p.nota
                    FROM App\Entity\Ubicacion p
                    WHERE  p.id_propiedad = '$idPropiedad' AND $condiBusca
	                ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;
        $idPropiedad = $this->idPropiedad;

		if (empty($txtSearch)){
		    	$sql = "SELECT count(p.id_ubic)
                        FROM App\Entity\Ubicacion p 
                        WHERE p.id_propiedad = '$idPropiedad'
                       ";
    	} else {
		    	$sql = " SELECT count(p.id_ubic)
                        FROM App\Entity\Ubicacion p 
                        WHERE p.id_propiedad = '$idPropiedad' AND $condiBusca
                    ";

    	}

    	return $sql;
	}


    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("p.id_ubic,p.nota,p.id_propiedad"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'p.id_ubic';
                break;
            case 1:
                $this->fieldOrder = 'p.ubicacion';
                break;
            case 2:
                $this->fieldOrder = 'p.nota';
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

	    foreach ($resultArray as $fila) {
            $html_col1 = '<p>'.$fila['id_ubic'].'</p><input type="hidden" id="name_'.$fila['id_ubic'].'" value="'.$fila['ubicacion'].'" />';
            $nohay=false;
	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_ubic'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "ubicacion"=>$fila['ubicacion'],
                "nota"=>$fila['nota']
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','ubicacion'=>'','nota'=>'');
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