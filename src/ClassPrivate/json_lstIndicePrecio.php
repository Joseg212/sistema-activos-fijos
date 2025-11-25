<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\IndicePrecio;
use Doctrine\Persistence\ManagerRegistry;

class json_lstIndicePrecio
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
                    SELECT p.id_ipc,p.anio,p.mes,p.factor,p.reconver,p.grupo
                    FROM App\Entity\IndicePrecio p
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_ipc,p.anio,p.mes,p.factor,p.reconver,p.grupo
                    FROM App\Entity\IndicePrecio p
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
		    	$sql = "SELECT count(p.id_ipc)
                        FROM App\Entity\IndicePrecio p 
                       ";
    	} else {
		    	$sql = " SELECT count(p.id_ipc)
                        FROM App\Entity\IndicePrecio p 
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

        $this->funcGlobal->setarrFieldSearch(array("p.id_ipc,p.anio,p.mes,p.factor,p.reconver,p.grupo"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'p.id_ipc';
                break;
            case 1:
                $this->fieldOrder = 'p.anio';
                break;
            case 2:
                $this->fieldOrder = 'p.mes';
                break;
            case 3:
                $this->fieldOrder = 'p.factor';
                break;
            case 4:
                $this->fieldOrder = 'p.reconver';
                break;
            case 4:
                $this->fieldOrder = 'p.grupo';
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
            $html_col1 = '<p>'.$fila['id_ipc'].'</p><input type="hidden" id="name_'.$fila['id_ipc'].'" value="'.$fila['factor'].'-'.$fila['grupo'].'" />';
            $nohay=false;
	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_ipc'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "anio"=>$fila['anio'],
                "mes"=>$fila['mes'],
                "factor"=>$fila['factor'],
                "reconver"=>$fila['reconver'],
                "grupo"=>$fila['grupo']
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','anio'=>'','mes'=>'','factor'=>'','reconver'=>'','grupo'=>'');
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