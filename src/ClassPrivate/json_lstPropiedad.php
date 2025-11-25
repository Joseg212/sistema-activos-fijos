<?php

namespace App\ClassPrivate;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Propiedad;
use Doctrine\Persistence\ManagerRegistry;

class json_lstPropiedad
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
                    SELECT p.id_propiedad,p.tipo,p.nombre,p.encargado,p.telefono
                    FROM App\Entity\Propiedad p
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_propiedad,p.tipo,p.nombre,p.encargado,p.telefono
                    FROM App\Entity\Propiedad p
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
		    	$sql = "SELECT count(p.id_propiedad)
                        FROM App\Entity\Propiedad p 
                       ";
    	} else {
		    	$sql = " SELECT count(p.id_propiedad)
                        FROM App\Entity\Propiedad p 
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

        $this->funcGlobal->setarrFieldSearch(array("p.id_propiedad,p.tipo,p.nombre,p.encargado,p.telefono"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'p.id_propiedad';
                break;
            case 1:
                $this->fieldOrder = 'p.tipo';
                break;
            case 2:
                $this->fieldOrder = 'p.nombre';
                break;
            case 3:
                $this->fieldOrder = 'p.encargado';
                break;
            case 4:
                $this->fieldOrder = 'p.telefono';
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
            $html_col1 = '<p>'.$fila['id_propiedad'].'</p><input type="hidden" id="name_'.$fila['id_propiedad'].'" value="'.$fila['nombre'].'" />';
            $nohay=false;
	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_propiedad'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "tipo"=>$fila['tipo'],
                "descrip"=>$fila['nombre'],
                "encargado"=>$fila['encargado'],
                "telefono"=>$fila['telefono'],
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','tipo'=>'','descrip'=>'','encargado'=>'','telefono'=>'');
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