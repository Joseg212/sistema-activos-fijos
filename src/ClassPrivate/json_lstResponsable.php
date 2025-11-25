<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Responsable;
use Doctrine\Persistence\ManagerRegistry;

class json_lstResponsable
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

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT p.id_resp,p.nombre,p.apellido,p.cargo,p.telefono,p.movil
                    FROM App\Entity\Responsable p
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_resp,p.nombre,p.apellido,p.cargo,p.telefono,p.movil
                    FROM App\Entity\Responsable p
                    WHERE $condiBusca
	                ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;

		if (empty($txtSearch)){
		    	$sql = "SELECT count(p.id_resp)
                        FROM App\Entity\Responsable p 
                       ";
    	} else {
		    	$sql = " SELECT count(p.id_resp)
                        FROM App\Entity\Responsable p 
                     WHERE $condiBusca
                    ";

    	}

    	return $sql;
	}


    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("p.id_resp,p.nombre,p.apellido,p.cargo,p.telefono,p.movil"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'p.id_resp';
                break;
            case 1:
                $this->fieldOrder = 'p.nombre';
                break;
            case 2:
                $this->fieldOrder = 'p.apellido';
                break;
            case 3:
                $this->fieldOrder = 'p.cargo';
                break;
            case 4:
                $this->fieldOrder = 'p.telefono';
                break;
            case 4:
                $this->fieldOrder = 'p.movil';
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
        $cadena = "";

	    foreach ($resultArray as $fila) {
            $cadena = $fila['cargo'].'/'.$fila['telefono'].'/'.$fila['movil'];
            $html_col1 = '<p>'.$fila['id_resp'].'</p><input type="hidden" id="name_'.$fila['id_resp'].'" value="'.$fila['nombre'].' '.$fila['apellido'].'" />';
            $html_col1 .='<input type="hidden" value="'.$cadena.'" id="cadena_'.$fila['id_resp'].'" />'; 
            $nohay=false;
	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_resp'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "nombre"=>$fila['nombre'],
                "apellido"=>$fila['apellido'],
                "cargo"=>$fila['cargo'],
                "telefono"=>$fila['telefono'],
                "movil"=>$fila['movil']
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','nombre'=>'','apellido'=>'','cargo'=>'','telefono'=>'','movil'=>'');
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