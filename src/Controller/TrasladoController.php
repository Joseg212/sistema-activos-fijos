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
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Traslado;
use App\Entity\PermisoMenu;
use App\Entity\Activofijo;
use App\Form\TrasladoType;
use App\ClassPrivate\json_lstTraslado;
use App\ClassPrivate\genReportJasper;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;



class TrasladoController extends AbstractController
{
    private $doctrine;
    private $funcGlobal;

    public function __construct(ManagerRegistry $doctrine,globalFunc $funcGlobal) {
        $this->doctrine=$doctrine;
        $this->funcGlobal = $funcGlobal;
        $this->funcGlobal->setDoctrine($doctrine);
    }

    /*Tabla de ubicacion  */
    /**
     * @Route("/traslado", name="app_traslado")
     */
    public function IndexTraslado(ManagerRegistry $doctrine): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/traslado/lst_traslado.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }
    /**
     * @Route("/traslado/list", name="ajax_lstTraslado")
     */
    public function ajaxListTraslado(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
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
            $estatus = $request->get('estatus');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $estatus = $request->get('estatus');
        }

        $rootWeb = $funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$doctrine->getManager();

        $object = new json_lstTraslado($searchDatos,$pageLong,$pageStart,$arrOrder);


        $object->setEstatus($estatus);
        $object->setFuncGlobal($funcGlobal);
        $object->setEmailUser($this->getUser()->getUserIdentifier());
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }

    /*Tabla de ubicacion  */
    /**
     * @Route("/traslado/list/activos", name="lst_sel_activos")
     */
    public function lstSelActivos(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/traslado/lst_sel_activo.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }
    /* Permite agregar un activo fijo */
    /**
     * @Route("/traslado/new/{idActivo}", name="new_traslado")
     */    
    public function newTraslado(string $idActivo,Request $request, ManagerRegistry $doctrine):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $verificar = $em->getRepository(Traslado::class)->findOneBy(['id_af'=>$idActivo,'estatus'=>'Pendiente']);

        if ($verificar)
        {
            // Existe un registro de traslado con el activo 
            return $this->redirectToRoute('app_traslado');

        } else {
            //$arrClases = $this->lstClases($doctrine);

            $entidad = new Traslado();
            $frmData = $this->FormularioTraslado($entidad,"new",$idActivo);

            $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

           
            return $this->render('views/traslado/new_traslado.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$idActivo,'datosAF'=>$datosActivoF
            ]);
        }
    }
    /* Permite grabar la informacion del FormularioActivoFijo */
    /**
     * @Route("/traslado/write/{idActivo}", name="write_traslado")
     */
    public function writeTraslado(string $idActivo,Request $request,ManagerRegistry $doctrine):Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

    
        $entidad = new Traslado();
        $frmData = $this->FormularioTraslado($entidad,"new",$idActivo);
        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            $dataGet->setIdTraslado("");

            // Se graba el correo del usuario 
            $dataGet->setEmailUser($idUsuario);

            if (empty($dataGet->getObserv()))
            {
                $dataGet->setObserv("");
            }

            if (empty($dataGet->getEstatus()))
            {
                $dataGet->setEstatus("Pendiente");
            }
            if (empty($dataGet->getDestinoExternoInfo()))
            {
                $dataGet->setDestinoExternoInfo("");
            }

            if ($dataGet->getDestinoExternoUbic()==null || empty($dataGet->getDestinoExternoUbic()))
            {
                $dataGet->setDestinoExternoUbic("");
                $dataGet->setDestinoExterno(0);
            } else {
                $dataGet->setDestinoExterno(1);
            }
            $dataGet->setTipoDes($this->devTipoTraslado($dataGet->getTipoTraslado()));

            $dataGet->setFecRecep(new \DateTime());

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_traslado');
    
        } else {
            $em = $this->doctrine->getManager();
            $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

            return $this->render('views/traslado/new_traslado.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$idActivo,'datosAF'=>$datosActivoF
            ]);
        }
    }

    /* Permite eliminar un Traslado */
    /**
     * @Route("/traslado/delete",name="delete_traslado")
     */
    public function deleteTraslado(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Traslado::class)->find($idCode);

            if ($entidad->getEstatus()!='Pendiente'){
                $data['ok']='34';
                $data['msg']='No puede eliminar un traslado aprobado o rechazado!!!';
            } else {
                $em->remove($entidad);
                if ($em->flush()){
                    $data['ok']='33';
                    $data['msg']='No se permitio la eliminación del traslado';
                } else {
                    $data['ok']='01';
                    $data['msg']='Se elimino correctamente el traslado';
                }
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
    /**
     * @Route("/traslado/edit/{idCode}", name="edit_traslado")
     */    
    public function editTraslado(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Traslado::class)->find($idCode);

        //$arrClases = $this->lstClases($doctrine);

        $frmData = $this->FormularioTraslado($entidad,"edit",$idCode);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad->getIdAf());

        $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

        return $this->render('views/traslado/edit_traslado.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$entidad->getIdAf(),'datosAF'=>$datosActivoF,
            'dataOther'=>$dataOther
        ]);
    } 

    /* Permite grabar la informacion del traslado */
    /**
     * @Route("/traslado/update/{idCode}", name="update_traslado")
     */
    public function updateTraslado(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

    
        $entidad = $doctrine->getManager()->getRepository(Traslado::class)->find($idCode);

        $frmData = $this->FormularioTraslado($entidad,"edit",$idCode);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            if (empty($dataGet->getObserv()))
            {
                $dataGet->setObserv("");
            }

            if (empty($dataGet->getEstatus()))
            {
                $dataGet->setEstatus("Pendiente");
            }
            if (empty($dataGet->getDestinoExternoInfo()))
            {
                $dataGet->setDestinoExternoInfo("");
            }

            if (empty($dataGet->getDestinoExternoUbic())){
                $dataGet->setDestinoExternoUbic("");
                $dataGet->setDestinoExterno(0);
            } else {
                $dataGet->setDestinoExterno(1);
            }
            $dataGet->setTipoDes($this->devTipoTraslado($dataGet->getTipoTraslado()));
            $dataGet->setFecRecep(new \DateTime());

            if (empty($dataGet->getEstatus())){
                $dataGet->setEstatus('Pendiente');
            }

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_traslado');
    
        } else {
            $em = $this->doctrine->getManager();
            
            $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

            $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad->getIdAf());

            return $this->render('views/traslado/edit_traslado.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$entidad->getIdAf(),'datosAF'=>$datosActivoF,
                'dataOther'=>$dataOther
            ]);
        }
    }

    /**
     * @Route("/traslado/view/{idCode}", name="view_traslado")
     */    
    public function viewTraslado(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Traslado::class)->findByTrasladoArray($idCode);

        //$arrClases = $this->lstClases($doctrine);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad['id_af']);

        $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

        return $this->render('views/traslado/view_traslado.html.twig', [
            'arrMenu'=>$arrMenu, 'datTraslado' =>$entidad,'idActivo'=>$entidad['id_af'],'datosAF'=>$datosActivoF,
            'dataOther'=>$dataOther
        ]);
    }
    /* Tabla de Traslado  */
    /**
     * @Route("/traslado/aprob/list", name="app_aprob_traslado")
     */
    public function IndexAprobTraslado(ManagerRegistry $doctrine): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/traslado/lst_aprob_traslado.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/traslado/aprob/estatus/{idCode}", name="status_aprob_traslado")
     */    
    public function statusAprobTraslado(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Traslado::class)->find($idCode);

        //$arrClases = $this->lstClases($doctrine);

        $frmData = $this->FormularioTraslado($entidad,"status",$idCode);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad->getIdAf());

        $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

        return $this->render('views/traslado/status_aprob_traslado.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$entidad->getIdAf(),'datosAF'=>$datosActivoF,
            'dataOther'=>$dataOther
        ]);
    }     

    /* Permite grabar la informacion del traslado */
    /**
     * @Route("/traslado/aprob/update/{idCode}", name="status_update_traslado")
     */
    public function statusUpdateTraslado(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);
    
        $entidad = $doctrine->getManager()->getRepository(Traslado::class)->find($idCode);

        $frmData = $this->FormularioTraslado($entidad,"status",$idCode);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            $dataGet->setFecRecep(new \DateTime());

            $tipoTras = $dataGet->getTipoTraslado();

            $em->persist($entidad);
            $em->flush();

            if ($tipoTras!='05')
            {
                $activoFijo = $em->getRepository(Activofijo::class)->find($dataGet->getIdAf());
                $activoFijo->setIdUbic($dataGet->getIdUbicDest());

                $em->persist($activoFijo);
                $em->flush();
            }

            return $this->redirectToRoute('app_aprob_traslado');
    
        } else {
            $em = $this->doctrine->getManager();
            
            $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

            $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad->getIdAf());

            return $this->render('views/traslado/edit_traslado.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idActivo'=>$entidad->getIdAf(),'datosAF'=>$datosActivoF,
                'dataOther'=>$dataOther
            ]);
        }
    }
    /**
     * @Route("/traslado/aprob/view/{idCode}", name="view_aprob_traslado")
     */    
    public function viewAprobTraslado(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Traslado::class)->findByTrasladoArray($idCode);

        //$arrClases = $this->lstClases($doctrine);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($entidad['id_af']);

        $dataOther = $em->getRepository(Traslado::class)->findByGetOtherData($idCode);

        return $this->render('views/traslado/view_aprob_traslado.html.twig', [
            'arrMenu'=>$arrMenu, 'datTraslado' =>$entidad,'idActivo'=>$entidad['id_af'],'datosAF'=>$datosActivoF,
            'dataOther'=>$dataOther
        ]);
    }    

    /* Controla el Formulario  */
    private function FormularioTraslado(Traslado $entity, string $tipo,string $idActivo)
    {

        if ($tipo=="new") {
            $form=$this->createForm(TrasladoType::class,$entity,array(
                'action'=> $this->generateUrl('write_traslado',['idActivo'=>$idActivo]),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } elseif($tipo=="edit") {
            $form = $this->createForm(TrasladoType::class,$entity,array(
                    'action'=>$this->generateUrl('update_traslado',['idCode'=>$entity->getIdTraslado()]),
                    'method'=>'POST',
                    'process'=>'edit',
                ));
        } elseif($tipo=="status"){
            $form = $this->createForm(TrasladoType::class,$entity,array(
                'action'=>$this->generateUrl('status_update_traslado',['idCode'=>$entity->getIdTraslado()]),
                'method'=>'POST',
                'process'=>'status',
            ));
        }
        return $form;
    }    
    /* Devuelve el tipo de traslado de forma descriptiva */ 
    public function devTipoTraslado($tipoTras):string
    {
        $tipoTraslado = "No se selecciono";
        switch ($tipoTras)
        {
            case "01":
                $tipoTraslado="Trasladar Activo Fijo dentro de la propiedad y ubicación.";
                break;
            case "02":
                $tipoTraslado="Trasladar Activo Fijo en otra propiedad y ubicación.";
                break;
            case "03":
                $tipoTraslado="Trasladar Activo Fijo para reparar dentro de la misma propiedad.";
                break;
            case "04":
                $tipoTraslado="Trasladar Activo Fijo para reparar en otra propiedad y ubicación.";
                break;
            case "05":
                $tipoTraslado="Trasladar Activo Fijo para reparar en una ubicación externa.";
                break;
        }
        return $tipoTraslado;
    }
    /**
     * @Route("/traslado/aprob/emitir/{idCode}",name="print_aprob_traslado")
     */
    public function printAprobTraslado(string $idCode):response
    {

        $rootProject = $this->getParameter('kernel.project_dir'); 

        $jasperFileName = $rootProject."\\public\\reports\\generated\\rpt_traslado.jasper";

        $pdfFileName = $rootProject."\\public\\reports\\created\\traslado_".date("Ymd_Hms");

        $reportObj = new genReportJasper($jasperFileName,$pdfFileName,['idActivo'=>$idCode]);

        $reportObj->runReport();

        return (new BinaryFileResponse($pdfFileName.".pdf", Response::HTTP_OK))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'traslado_'.date("Ymd_Hms").'.pdf');        
    }    
}