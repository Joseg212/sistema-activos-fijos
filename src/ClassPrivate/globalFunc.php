<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;

class globalFunc {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }

    public function setDoctrine(ManagerRegistry $doctrine):self
    {
        $this->doctrine = $doctrine;
        
        return $this;
    }

	public function returnPathWeb($rootPath):string
	{
        $retorno = '';

		$posc_text=strrpos($rootPath,'app_dev.php'); // si de vuelve 10

		if ($posc_text===false || $posc_text <=0){
            $retorno = $rootPath.'/';
		} else {
            $retorno = substr($rootPath,0,$posc_text-1).'/';
        }
		return ($retorno);
	}

    public function roleText($role):string
    {
        $valor = '';
        switch ($role) {
            case 'ROLE_ADMIN':
                $valor = 'Operador onHand';
                break;
            case 'ROLE_GESTOR':
                $valor = 'Gestor de Vivienda';
                break;
            case 'ROLE_GESTORL':
                $valor = 'Gestor de Limpieza';
                break;
            case 'ROLE_OPEL':
                $valor = 'Operador de Limpieza';
                break;
            case 'ROLE_DUENO':
                $valor = 'Dueño de Vivienda';
                break;
            case 'ROLE_GOBERN':
                $valor = 'Gobernanta';
                break;
            case 'ROLE_SUPPORT':
                $valor = 'Mantenimiento';
                break;
            case 'ROLE_RECEP':
                $valor = 'Recepción';
                break;
        }
        return $valor;
    }

    /* Procedimiento para la Busqueda Normal o Selectiva */
    private $typeSearch='';
    private $textSearch='';
    private $arrSearch=array();
    private $arrFieldSearch = array();

    public function setTypeSearch($typeSearch)
    {
        $this->typeSearch=$typeSearch;
        return;
    }
    public function setTextSearch($txtSearch)
    {
        $this->textSearch=$txtSearch;
        $this->arrSearch=explode(" ",$txtSearch);
    }
    public function setarrFieldSearch($arrFieldS)
    {
        $this->arrFieldSearch = $arrFieldS;
    }
    private function getCadena($fieldName):string
    {
        $cadenaSub = "";

        $longArray = sizeof($this->arrSearch);

        foreach ($this->arrSearch as $key => $value) {
            if ($longArray==($key+1))
            {
                $cadenaSub.=$fieldName." like '%".$value."%'";
            } else {
                $cadenaSub.=$fieldName." like '%".$value."%' OR ";
            }
        }
        return $cadenaSub;
    }

    public function getSearchText():string
    {
        $cadena = " (";
        $longArray = sizeof($this->arrFieldSearch);
        /* Devuelve el texto para la busqueda de información */
        if ($this->typeSearch=='Gen'){
            // Si una busqueda Generalizada.
            foreach ($this->arrFieldSearch as $posc => $field)
            {
                if ($longArray==($posc+1))
                {
                    $cadena.=$this->getCadena($field).") ";
                } else {
                    $cadena.=$this->getCadena($field)." OR ";
                }
            }

        } else {
            // Si una busqueda parcializada
            $field= str_replace('_','.', $this->typeSearch);
            $cadena.=$this->getCadena($field).") ";
        }
        return $cadena;
    }
}

?>