<?php

namespace App\ClassPrivate;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Entity\Clasificacion;
use Doctrine\Persistence\ManagerRegistry;

class json_lstIdentificar
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
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.num_serie,
                    p.code_activof,p.costo_total,p.costo_flete,p.distribuidor,
                    c.descripcion as clasef,c.id_clase, p.edo_fisico, p.marca
                    FROM App\Entity\Activofijo p
                    INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
                    WHERE p.estatus='activo' AND p.id_ubic = '$idUbicacion'
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.num_serie,
                    p.code_activof,p.costo_total,p.costo_flete,p.distribuidor,
                    c.descripcion as clasef,c.id_clase,p.edo_fisico, p.marca
                    FROM App\Entity\Activofijo p
                    INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
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
                $this->fieldOrder = 'p.num_serie';
                break;

            case 4:
                $this->fieldOrder = 'p.code_activof';
                break;

            case 5:
                $this->fieldOrder = 'p.costo_total';
                break;


            case 6:
                $this->fieldOrder = 'c.id_clase';
                break;

            case 7:
                $this->fieldOrder = 'c.edo_fisico';
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
        $htmlMarca="";

	    foreach ($resultArray as $fila) {
            $html_col1 = '<p>'.$fila['id_af'].'</p><input type="hidden" id="name_'.$fila['id_af'].'" value="'.$fila['descrip'].'" />';
            $nohay=false;


            $htmlMarca  = '<div style="text-align:center;width:110px;">';
            $htmlMarca .= '<label class="switch" for="c_'.$fila['id_af'].'">';
            $htmlMarca .= '<input type="checkbox"  id="c_'.$fila['id_af'].'" data-id="'.$fila['id_af'].'" '; 
            $htmlMarca .= ($fila['marca']==0) ? ' class="selActive">' : ' checked="" class="selActive">';
            $htmlMarca .= '<span class="slider round"></span>';
            $htmlMarca .= '</label></div>';

	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_af'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "descrip"=>'<p style="display:block;">'.$fila['descrip'].'</p>',
                "fechac"=>$fila['fecha_compra']->format("d/m/Y"),
                "numserie"=>$fila['num_serie'],
                "clase"=>'<p style="display:block"><b>'.$fila['clasef'].'</b></p>',
                "code"=>$fila['code_activof'],
                "marca"=>$htmlMarca,
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','descrip'=>'','fechac'=>'','numserie'=>'','clase'=>'','code'=>'','marca'=>'');
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