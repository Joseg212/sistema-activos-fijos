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
use App\Entity\Ubicacion;
use App\Entity\Traslado;
use Doctrine\Persistence\ManagerRegistry;

class json_lstTraslado
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

    private $estatus;

    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }

    private $emailUser;

    public function setEmailUser($emailUser)
    {
        $this->emailUser = $emailUser;
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
        $estatus = $this->estatus;

        if ($estatus=='Todos'){
            $estatus = "";
        } else {
            $estatus = "tra.estatus = '{$estatus}' AND";
        }

        $email_user = $this->emailUser;

		if (empty($txtSearch)){
		    	$sql = "
                    SELECT tra.id_traslado,af.descrip,
                        tra.fecha_traslado as fec_tra, 
                        u.ubicacion as origen, 
                        ud.ubicacion as  destino,
                        CONCAT(resp.nombre,' ',resp.apellido) AS resp_dest,
                        tra.estatus, tra.destino_externo_ubic as ubic_ext,
                        tra.destino_externo
                    FROM App\Entity\Traslado tra
                    INNER JOIN App\Entity\Activofijo af WITH tra.id_af = af.id_af 
                    INNER JOIN App\Entity\Responsable resp WITH resp.id_resp=tra.id_resp_destino
                    INNER JOIN App\Entity\Ubicacion u  WITH tra.id_ubic_orig = u.id_ubic
                    LEFT JOIN App\Entity\Ubicacion ud WITH tra.id_ubic_dest = ud.id_ubic
                    WHERE {$estatus} tra.email_user='{$email_user}'
                    ORDER BY $fieldOrder $typeOrder
                    ";
    	} else {
		    	$sql = " 
                    SELECT tra.id_traslado,af.descrip,
                        tra.fecha_traslado as fec_tra, 
                        u.ubicacion as origen, 
                        ud.ubicacion as  destino,
                        CONCAT(resp.nombre,' ',resp.apellido) AS resp_dest,
                        tra.estatus, tra.destino_externo_ubic as ubic_ext
                    FROM App\Entity\Traslado tra
                    INNER JOIN App\Entity\Activofijo af WITH tra.id_af = af.id_af
                    INNER JOIN App\Entity\Responsable resp WITH resp.id_resp=tra.id_resp_destino
                    INNER JOIN App\Entity\Ubicacion u  WITH tra.id_ubic_orign = u.id_ubic
                    LEFT JOIN App\Entity\Ubicacion ud WITH tra.id_ubic_dest = ud.id_ubic
                    WHERE {$estatus} tra.email_user='{$email_user}' AND '$condiBusca
	                ORDER BY $fieldOrder $typeOrder
                    ";

    	}
    	return $sql;
	}
	private function numberRegisters()
	{

		$condiBusca=$this->searchDatos;
        $txtSearch = $this->txtSearch;
        $estatus = $this->estatus;

        if ($estatus=='Todos'){
            $estatus = "";
        } else {
            $estatus = "tra.estatus = '{$estatus}' AND";
        }

        $email_user = $this->emailUser;

		if (empty($txtSearch)){
		    	$sql = "SELECT count(tra.id_traslado)
                        FROM App\Entity\Traslado tra 
                        INNER JOIN App\Entity\Activofijo af WITH tra.id_af = af.id_af AND af.estatus='activo'
                        INNER JOIN App\Entity\Responsable resp WITH resp.id_resp=tra.id_resp_destino
                        INNER JOIN App\Entity\Ubicacion u  WITH tra.id_ubic_orig = u.id_ubic
                        LEFT JOIN App\Entity\Ubicacion ud WITH tra.id_ubic_dest = ud.id_ubic
                        WHERE {$estatus} tra.email_user='{$email_user}'
                       ";
    	} else {
                $sql = "SELECT count(tra.id_traslado)
                        FROM App\Entity\Traslado tra 
                        INNER JOIN App\Entity\Activofijo af WITH tra.id_af = af.id_af AND af.estatus='activo'
                        INNER JOIN App\Entity\Responsable resp WITH resp.id_resp=tra.id_resp_destino
                        INNER JOIN App\Entity\Ubicacion u  WITH tra.id_ubic_orig = u.id_ubic
                        LEFT JOIN App\Entity\Ubicacion ud WITH tra.id_ubic_dest = ud.id_ubic
                        WHERE {$estatus} tra.email_user='{$email_user}' AND $condiBusca
                    ";
    	}
    	return $sql;
	}


    public function responseData($docTrine)
    {

    	$data=array();

        $this->funcGlobal->setTypeSearch($this->typeSearch);
        $this->funcGlobal->setTextSearch($this->txtSearch);

        $this->funcGlobal->setarrFieldSearch(array("tra.id_traslado,af.descrip,af.num_serie,af.distribuidor,af.code_activof,resp.nombre,resp.apellido,u.ubicacion,ud.ubicacion"));

        $this->searchDatos = $this->funcGlobal->getSearchText();

        /* Ordenamiento de la fila  */
        $this->typeOrder = $this->arrOrder[0]['dir'];
        switch ($this->arrOrder[0]['column']) {
            case 0:
                $this->fieldOrder = 'tra.id_traslado';
                break;
            case 1:
                $this->fieldOrder = 'af.descrip';
                break;
            case 2:
                $this->fieldOrder = 'tra.fecha_traslado';
                break;
            case 3:
                $this->fieldOrder = 'u.ubicacion';
                break;

            case 4:
                $this->fieldOrder = 'u.ubicacion';
                break;

            case 5:
                $this->fieldOrder = 'resp.nombre';
                break;


            case 6:
                $this->fieldOrder = 'tra.estatus';
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
            $html_col1 = '<p>'.$fila['id_traslado'].'</p><input type="hidden" id="name_'.$fila['id_traslado'].'" value="'.$fila['descrip'].'" />';
            $html_col1 .= '<input type="hidden" id="status_'.$fila['id_traslado'].'" value="'.$fila['estatus'].'" />';
            $nohay=false;

            switch ($fila['estatus'])
            {
                case 'Pendiente':
                    $colorType='';
                    break;
                case 'Aprobado':
                    $colorType='af-tras-aprobado';
                    break;
                case 'Rechazado':
                    $colorType='af-tras-rechazado';
                    break;
            }

	    	$data['data'][]=array(
                "DT_RowId"=> $fila['id_traslado'],
                "DT_RowClass"=> "tr-select",
                "id"=>$html_col1,
                "descrip"=>$fila['descrip'],
                "fechat"=>$fila['fec_tra']->format("d/m/Y"),
                "ubic_ori"=>$fila['origen'],
                "ubic_dest"=>($fila['destino_externo']==1) ? $fila['ubic_ext'] : $fila['destino'],
                "resp_dest"=>$fila['resp_dest'],
                "estatus"=>'<p class="af-msg-tras '.$colorType.'">'.$fila['estatus'].'</p>',
            );

	    };

        if ($nohay)
        {
            $data['data'][]=array(
                'id'=>'','descrip'=>'','fechat'=>'','ubic_ori'=>'','ubic_dest'=>'','resp_dest'=>'','estatus'=>'');
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