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
use App\Entity\Activofijo;
use App\Entity\Finiquito;
use App\Entity\Mantenimiento;
use App\Entity\PermisoMenu;
use App\ClassPrivate\json_lstFiniquitar;
use App\ClassPrivate\AmortizProcess;
use App\Form\FiniquitoType;
use App\ClassPrivate\genReportJasper;
use App\Entity\FactMejora;
use App\Entity\FactMejoratmp;
use Doctrine\Persistence\Event\ManagerEventArgs;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FiniquitarController extends AbstractController
{
    private $doctrine;
    private $funcGlobal;
    private $requestStack;

    public function __construct(ManagerRegistry $doctrine,globalFunc $funcGlobal,RequestStack $requestStack) {
        $this->doctrine=$doctrine;
        $this->funcGlobal = $funcGlobal;

        $this->funcGlobal->setDoctrine($doctrine);
        $this->requestStack = $requestStack;
    }
    /**
     * @Route("/finiquitar/listado", name="app_finiquitar")
     */
    public function indexFiniquitar(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/finiquito/lst_finiquitar.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    } 
    /**
     * @Route("/finiquitar/ajax/list", name="ajax_lstFiniquitar")
     */
    public function ajaxListFiniquitar(Request $request): Response
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

        $rootWeb = $this->funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$this->doctrine->getManager();

        $object = new json_lstFiniquitar($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdPropiedad($idPropiedad);
        $object->setIdUbicacion($idUbicacion);
        $object->setFuncGlobal($this->funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }


    /**
     * @Route("/finiquito/proceso/{idActivo}", name="process_finiquito")
     */
    public function processFiniquito(string $idActivo): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = new Finiquito();

        $mejora = $em->getRepository(Mantenimiento::class)->findByMejoraActivo($idActivo);

        if (empty($mejora['id_mant'])){
            $frmData = $this->FormularioFiniquito($entidad,"process_normal",$idActivo);

        } else {
            $frmData = $this->FormularioFiniquito($entidad,"process_mejora",$idActivo);
        }

        $activoFijo = $em->getRepository(Activofijo::class)->find($idActivo);

        $datosActivoF = array(
            'idAf'         =>$activoFijo->getIdAf(),
            'descrip'       =>$activoFijo->getDescrip(),
            'fechac'        =>$activoFijo->getFechaCompra()->format("d/m/Y"),
            'distrib'       =>$activoFijo->getDistribuidor(),
            'nrofact'       =>$activoFijo->getNrofact(),
            'costo'         =>number_format($activoFijo->getCosto(),2,',','.'),
            'impuesto'      =>number_format($activoFijo->getImpuesto(),2,',','.'),
            'costot'        =>number_format($activoFijo->getCostoTotal(),2,',','.'),
            'flete'         =>number_format($activoFijo->getCostoFlete(),2,',','.'),
            'code'          =>$activoFijo->getCodeActivof(),
            'edofisico'     =>$activoFijo->getEdoFisico(),
        );

        return $this->render('views/finiquito/process_finiquito.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF, 'form'=> $frmData->createView(),
            'mejora'=>$mejora
        ]);
    }

    /**
     * @Route("/finiquito/save/{idActivo}", name="save_finiquito")
     */
    public function saveFiniquito(string $idActivo,Request $request): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = new Finiquito();

        $mejora = $em->getRepository(Mantenimiento::class)->findByMejoraActivo($idActivo);
        if (empty($mejora['id_mant'])){
            $frmData = $this->FormularioFiniquito($entidad,"process_normal",$idActivo);

        } else {
            $frmData = $this->FormularioFiniquito($entidad,"process_mejora",$idActivo);
        }

        $activoFijo = $em->getRepository(Activofijo::class)->find($idActivo);

        $datosActivoF = array(
            'idAf'         =>$activoFijo->getIdAf(),
            'descrip'       =>$activoFijo->getDescrip(),
            'fechac'        =>$activoFijo->getFechaCompra()->format("d/m/Y"),
            'distrib'       =>$activoFijo->getDistribuidor(),
            'nrofact'       =>$activoFijo->getNrofact(),
            'costo'         =>number_format($activoFijo->getCosto(),2,',','.'),
            'impuesto'      =>number_format($activoFijo->getImpuesto(),2,',','.'),
            'costot'        =>number_format($activoFijo->getCostoTotal(),2,',','.'),
            'flete'         =>number_format($activoFijo->getCostoFlete(),2,',','.'),
            'code'          =>$activoFijo->getCodeActivof(),
            'edofisico'     =>$activoFijo->getEdoFisico(),
        );

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $finiquito = $frmData->getData();

            $this->doctrine->getConnection()->beginTransaction();

            try {
                $finiquito->setIdFin("");

                if (empty($finiquito->getNuevaDescrip()))
                {
                    $finiquito->setNuevaDescrip("");
                }
                if (empty($finiquito->getObservacion()))
                {
                    $finiquito->setObservacion("");
                }

           
                $em->persist($entidad);
                $em->flush();

                $activo = $em->getRepository(Activofijo::class)->find($idActivo);


                if (!empty($mejora['id_mant'])){
                    $activo->setEstatus('mejorado');
                    $em->persist($activo);
                    $em->flush();
    
                    $mantCamb = $em->getRepository(Mantenimiento::class)->find($mejora['id_mant']);
                    $mantCamb->setNroFact("finity");

                    $em->persist($mantCamb);
                    $em->flush();
                    // Se ingresa el nuevo activo fijo 
                    $newActivo = new Activofijo();
                    
                    $newActivo->setIdAf("");
                    $newActivo->setIdClase($activo->getIdClase());
                    $newActivo->setIdUbic($activo->getIdUbic());
                    $newActivo->setDescrip($finiquito->getNuevaDescrip());
                    $newActivo->setCodeActivof("");
                    $newActivo->setFechaCompra($mantCamb->getFechaFact());
                    $newActivo->setDistribuidor($activo->getDistribuidor());
                    $newActivo->setRif($activo->getRif());
                    $newActivo->setNroFact("");
                    $newActivo->setCosto($finiquito->getCostoMej());
                    $newActivo->setImpuesto($finiquito->getImpMej());
                    $newActivo->setCostoTotal($finiquito->getTotalMej());
                    $newActivo->setCostoFlete($finiquito->getTotalFlete());
                    $newActivo->setEdoFisico("Nuevo");
                    $newActivo->setNumSerie($activo->getNumSerie());
                    $newActivo->setEstatus("activo");
                    $newActivo->setMarca(0);
                    $em->persist($newActivo);
                    $em->flush();
                } else {
                    $activo->setEstatus('finiquito');
                    $em->persist($activo);
                    $em->flush();
                }

                $this->doctrine->getConnection()->commit();
            } catch (\Exception $err) {
                $this->doctrine->getConnection()->rollback();
            }

            return $this->redirectToRoute('app_finiquitar',['idActivo'=>$idActivo]);

        } else {
            return $this->render('views/finiquito/process_finiquito.html.twig', [
                'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF,'form' => $frmData->createView(),
                'mejora'=>$mejora            
            ]);
    
        }
    }
    /**
     * @Route("/finiquitar/ajax/load", name="load_ajax_finiquito")
     */
    public function loadAjaxFiniquito(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $fecha = new \DateTime($request->get('fechaf'));
            $idActivo = $request->get('idActivo');
        } else {
            $fecha = new \DateTime('');
            $idActivo = "";
        }
        $data=['msg'=>'Algo Fallo en el Proceso!!','ok'=>'10'];
        $amortiz = [];
        try {

            $objAmortiz = new AmortizProcess($this->doctrine);

            $amortiz = $objAmortiz->loadInfoAmortiz($fecha,$idActivo);
            if ($amortiz['error']=='10'){
                $data['ok']='33';
                $data['msg']='Fecha inferior a la última amortización ejecutada en el activo fijo!!';

            } else {
                if ($amortiz['error']=='20'){
                    $data['ok']='34';
                    $data['msg']='Se requiere de los cálculos defina el  método de amortización.!!';
    
                } else {
                    if ($amortiz['error']=='30'){
                        $data['ok']='36';
                        $data['msg']='No hay índice de precio para la fecha indicada.!!';
        
                    } else {
                        $data['amortiz'] = $amortiz;
    
                        $data['ok']='01';
                        $data['msg']='Correct!!!'.$fecha->format('d/m/Y');
    
                    }
                }
            }
        } catch (\Exception $err) {
            $data['ok']='15';
            $data['msg']='Error:'.$err->getMessage().' Line No:'.$err->getLine();
        }
        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;           
    }

    /* Controla el Formulario  */
    private function FormularioFiniquito(Finiquito $entity, string $tipo,string $idActivo)
    {

        if ($tipo=="process_normal" || $tipo=="process_mejora") {
            $form=$this->createForm(FiniquitoType::class,$entity,array(
                'action'=> $this->generateUrl('save_finiquito',['idActivo'=>$idActivo]),
                'method'=>'POST',
                'process'=>$tipo,
                 ));
            
        } else {
            /*
            $form = $this->createForm(ActivoFijoType::class,$entity,array(
                    'action'=>$this->generateUrl('update_activofijo',['idCode'=>$entity->getIdAf()]),
                    'method'=>'POST',
                    'process'=>'edit',
                    'clases' => $arrClases,
                ));
            */
        }
        return $form;
    }   
    /**
     * @Route("/finiquitar/reporte/document/{idActivo}",name="rpt_finiquito")
     */
    public function rptFiniquito(string $idActivo):response
    {

        $rootProject = $this->getParameter('kernel.project_dir'); 

        $jasperFileName = $rootProject."\\public\\reports\\generated\\rpt_finiquito.jasper";

        $pdfFileName = $rootProject."\\public\\reports\\created\\finiquito_".date("Ymd_Hms");

        $reportObj = new genReportJasper($jasperFileName,$pdfFileName,['idActivo'=>$idActivo]);

        $reportObj->runReport();

        return (new BinaryFileResponse($pdfFileName.".pdf", Response::HTTP_OK))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'finiquito_'.date("Ymd_Hms").'.pdf');        
    }
    
}