<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Entity\Amortizaciones;
use App\Entity\Clasificacion;
use Doctrine\Persistence\ManagerRegistry;

class json_QueryActivoFijo
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
    private $idUbicacion;
    private $idClase;
    private $codeActivof;
    private $estatus;

    public function setIdPropiedad($idPropiedad)
    {
        $this->idPropiedad = $idPropiedad;
    }

    public function setIdUbicacion($idUbicacion)
    {
        $this->idUbicacion = $idUbicacion;
    }
    public function setIdClase($idClase)
    {
        $this->idClase = $idClase;
    }
    public function setCodeActivof($codeActivof)
    {
        $this->codeActivof = $codeActivof;
    }
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
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
        $searchCondi = $this->getStringSearch();

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.num_serie,
                    p.code_activof,p.costo_total,p.costo_flete,p.distribuidor,
                    c.descripcion as clasef,c.id_clase, p.edo_fisico, p.marca,p.estatus,
                    pro.nombre, pro.id_propiedad, ubic.ubicacion, ubic.id_ubic
                    FROM App\Entity\Activofijo p
                    INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
                    INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = p.id_ubic
                    INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
                    WHERE $searchCondi
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT p.id_af,p.descrip,p.fecha_compra,p.num_serie,
                    p.code_activof,p.costo_total,p.costo_flete,p.distribuidor,
                    c.descripcion as clasef,c.id_clase, p.edo_fisico, p.marca,p.estatus,
                    pro.nombre, pro.id_propiedad, ubic.ubicacion, ubic.id_ubic
                    FROM App\Entity\Activofijo p
                    INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
                    INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = p.id_ubic
                    INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
                    WHERE $searchCondi and $condiBusca
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
        $searchCondi = $this->getStringSearch();

		if (empty($txtSearch)){
		    	$sql = "SELECT count(p.id_ubic)
                        FROM App\Entity\Activofijo p
                        INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
                        INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = p.id_ubic
                        INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
                        WHERE $searchCondi 
                       ";
    	} else {
                $sql = "SELECT count(p.id_ubic)
                        FROM App\Entity\Activofijo p
                        INNER JOIN App\Entity\Clasificacion c WITH p.id_clase = c.id_clase
                        INNER JOIN App\Entity\Ubicacion ubic WITH ubic.id_ubic = p.id_ubic
                        INNER JOIN App\Entity\Propiedad pro WITH pro.id_propiedad = ubic.id_propiedad
                        WHERE $searchCondi AND $condiBusca
                    ";
    	}
    	return $sql;
	}


    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("p.id_ubic","p.id_clase","p.num_serie","p.distribuidor","p.code_activof","p.descrip","p.nrofact","p.edo_fisico"));

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
                $this->fieldOrder = 'p.id_clase';
                break;

            case 5:
                $this->fieldOrder = 'p.code_activof';
                break;

            case 6:
                $this->fieldOrder = 'p.estatus';
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
        $colorType='';
	    foreach ($resultArray as $fila) {
            $html_col1 = '<p>'.$fila['id_af'].'</p><p><b>'.$fila['nombre'].'</b></p><input type="hidden" id="name_'.$fila['id_af'].'" value="'.$fila['descrip'].'" />';
            $nohay=false;

            switch ($fila['estatus'])
            {
                case 'activo':
                    $colorType='';
                    break;
                case 'finiquito':
                    $colorType='af-tras-rechazado';
                    break;
                case 'mejorado':
                    $colorType='af-tras-aprobado';
                    break;
            }            
            //$id_maximo = $docTrine->getRepository(Amortizaciones::class)->findByObtenerMaximoId($fila['id_af']);

            //$amortActivo = $docTrine->getRepository(Amortizaciones::class)->find($id_maximo);

	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_af'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "descrip"=>'<p style="display:block;">'.$fila['descrip'].'</p><p><b>'.$fila['ubicacion'].'</b></p>',
                "fechac"=>$fila['fecha_compra']->format("d/m/Y"),
                "numserie"=>$fila['num_serie'],
                "clase"=>'<p style="display:block"><b>'.$fila['clasef'].'</b></p>',
                "code"=>$fila['code_activof'],
                "estatus"=>'<p class="af-msg-tras '.$colorType.'">'.$fila['estatus'].'</p>',
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','descrip'=>'','fechac'=>'','numserie'=>'','clase'=>'','code'=>'','estatus'=>'');
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
    private function getStringSearch():string
    {
        $searchData="";
        if ($this->idPropiedad!='todos'){
            $searchData=$this->concatenar("pro.id_propiedad='{$this->idPropiedad}'",$searchData);
        }
        if ($this->idUbicacion!='todos'){
            $searchData=$this->concatenar("ubic.id_ubic='{$this->idUbicacion}'",$searchData);
        }
        if ($this->idClase!='todos'){
            $searchData=$this->concatenar("c.id_clase='{$this->idClase}'",$searchData);
        }
        if ($this->estatus!='todos'){
            $searchData=$this->concatenar("p.estatus='{$this->estatus}'",$searchData);
        }
        if ($this->codeActivof!=''){
            $searchData=$this->concatenar("p.code_activof like '%".$this->codeActivof."%'",$searchData);
        }
        if ($searchData==""){
            $searchData="p.estatus<>'nulo' ";
        }
        return $searchData;
    }
    private function concatenar(string $stringData,string $cadena):string
    {
        $stringDataDev = "";
        if ($cadena==""){
            $stringDataDev = $stringData;
        } else { 
            $stringDataDev = $cadena." And ".$stringData;
        }
        return $stringDataDev;
    }
}

?>