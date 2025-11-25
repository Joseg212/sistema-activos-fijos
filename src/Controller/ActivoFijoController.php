<?php

namespace App\Controller;

/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\ClassPrivate\globalFunc;
use App\Entity\Usuario;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Activofijo;
use App\Entity\PermisoMenu;
use App\Form\ActivoFijoType;
use App\ClassPrivate\json_lstActivoFijo;
use App\ClassPrivate\genReportJasper;
use App\Entity\Clasificacion;
use Doctrine\Persistence\Event\ManagerEventArgs;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ActivoFijoController extends AbstractController
{

    /**
     * @Route("/activofijo", name="app_activofijo")
     */
    public function ActivoFijo(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/activofijo/lst_activofijo.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/activofijo/list", name="ajax_lstActivoFijo")
     */
    public function ajaxListActivoFijo(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
    {
        if ($request->isMethod('POST')) {
            //$postDatos =  $this->getRequest()->request->all();
            //$postDatos = json_decode($request->getContent(), true);

            //$searchDatos = $postDatos['search']['value'];
            $searchDatos =  ($request->get('search'))['value'];
            $pageLong = $request->get('length');
            $pageStart = $request->get('start');
            $arrOrder = $request->get('order');
            $txtSearch = $request->get('txtSearch');
            $typeSearch = $request->get('typeSearch');
            $idPropiedad = $request->get('idPropiedad');
            $idUbicacion = $request->get('idUbicacion');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $idPropiedad = '';
            $idUbicacion = '';
        }

        $rootWeb = $funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$doctrine->getManager();

        $object = new json_lstActivoFijo($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdPropiedad($idPropiedad);
        $object->setIdUbicacion($idUbicacion);
        $object->setFuncGlobal($funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }
    /* Permite agregar un activo fijo */
    /**
     * @Route("/activofijo/new/{idUbicacion}", name="new_activofijo")
     */    
    public function newActivoFijo(string $idUbicacion,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $arrClases = $this->lstClases($doctrine);

        $entidad = new Activofijo();
        $frmData = $this->FormularioActivoFijo($entidad,"new",$idUbicacion,$arrClases);


        return $this->render('views/activofijo/new_activofijo.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idUbicacion'=>$idUbicacion
        ]);
    }
    /* Permite grabar la informacion del FormularioActivoFijo */
    /**
     * @Route("/activofijo/write/{idUbicacion}", name="write_activofijo")
     */
    public function writeActivoFijo(string $idUbicacion,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $arrClases = $this->lstClases($doctrine);

        $entidad = new ActivoFijo();
        $frmData = $this->FormularioActivoFijo($entidad,"new",$idUbicacion,$arrClases);
        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            $dataGet->setIdAf("");
            $dataGet->setMarca(0);

            /*$dataGet->setCosto($this->convertDecimal($dataGet->getCosto()->toString()));
            $dataGet->setImpuesto($this->convertDecimal($dataGet->getImpuesto()->toString()));
            $dataGet->setCostoTotal($this->convertDecimal($dataGet->getCostoTotal()->toString()));
            $dataGet->setCostoFlete($this->convertDecimal($dataGet->getCostoFlete()->toString()));*/

            if (empty($dataGet->getEstatus())){
                $dataGet->setEstatus('activo');
            }
            if (empty($dataGet->getCodeActivof())){
                $dataGet->setCodeActivof("");
            }
            


            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_activofijo');
    
        } else {
    
            return $this->render('views/activofijo/new_activofijo.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idUbicacion'=>$idUbicacion
            ]);

        }
    } 

    /* Permite modificar una activo fijo */
    /**
     * @Route("/activofijo/edit/{idCode}", name="edit_activofijo")
     */    
    public function editActivoFijo(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $arrClases = $this->lstClases($doctrine);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Activofijo::class)->find($idCode);
        $frmData = $this->FormularioActivoFijo($entidad,"edit","",$arrClases);

        return $this->render('views/activofijo/edit_activofijo.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /* Permite modificar la informacion */
    /**
     * @Route("/activofijo/update/{idCode}",name="update_activofijo")
     */
    public function updateActivoFijo(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $arrClases = $this->lstClases($doctrine);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Activofijo::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioActivoFijo($entidad,"edit","",$arrClases);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            /*$dataGet->setCosto($this->convertDecimal($dataGet->getCosto()));
            $dataGet->setImpuesto($this->convertDecimal($dataGet->getImpuesto()));
            $dataGet->setCostoTotal($this->convertDecimal($dataGet->getCostoTotal()));
            $dataGet->setCostoFlete($this->convertDecimal($dataGet->getCostoFlete()));*/

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_activofijo');
    
        } else {
    
            return $this->render('views/activofijo/edit_activofijo.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite eliminar un Activo Fijo */
    /**
     * @Route("/activofijo/delete",name="delete_activofijo")
     */
    public function deleteActivoFijo(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Activofijo::class)->find($idCode);

            $em->remove($entidad);
            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación del activo fijo';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la activo fijo';
            }

        } catch (\Exception $err) {
            $data['ok']='99';
            $data['msg']='Exception:'. $err->getMessage();
        }

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }
    /* Controla el Formulario  */
    private function FormularioActivoFijo(Activofijo $entity, string $tipo,string $idUbicacion,$arrClases)
    {

        if ($tipo=="new") {
            $form=$this->createForm(ActivoFijoType::class,$entity,array(
                'action'=> $this->generateUrl('write_activofijo',['idUbicacion'=>$idUbicacion]),
                'method'=>'POST',
                'process'=>'new',
                'clases' => $arrClases,
                 ));
            
        } else {
            $form = $this->createForm(ActivoFijoType::class,$entity,array(
                    'action'=>$this->generateUrl('update_activofijo',['idCode'=>$entity->getIdAf()]),
                    'method'=>'POST',
                    'process'=>'edit',
                    'clases' => $arrClases,
                ));
        }
        return $form;
    }

    private function lstClases(ManagerRegistry $doctrine): array
    {
        $lstClases = array("Seleccione"=>"select");

        $em = $doctrine->getManager();

        $query = $em->getRepository(Clasificacion::class)->findByClases();
        
        if ($query){
            $result = $query->getResult();
            foreach ($result As $fila){
                $lstClases[$fila['descripcion']] = $fila['id_clase'];
            }
        }
        return $lstClases;
    }


    /* Funcion para acomodar el numerico */

    private function convertDecimal(string $decimalMask):float
    {
        $decimalMask = str_replace("\,","",$decimalMask);
        return  (float)$decimalMask;
    }

    /**
     * @Route("/prueba/reporte",name="gen_report")
     */
    public function genReport():response
    {

        $rootProject = $this->getParameter('kernel.project_dir'); 

        $jasperFileName = $rootProject."\\public\\reports\\generated\\agruparrecib.jasper";
        $pdfFileName = $rootProject."\\public\\reports\\created\\recibos".date("Ymd_Hms");


        $reportObj = new genReportJasper($jasperFileName,$pdfFileName,['aniomesproc'=>'202012','codedifr'=>'PV10']);

        $reportObj->runReport();

        return (new BinaryFileResponse($pdfFileName.".pdf", Response::HTTP_OK))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'recibo_agrupados.pdf');        
    }

}