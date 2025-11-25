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
use App\Entity\TipoAmortiz;
use App\Entity\Amortizaciones;
use App\Entity\PermisoMenu;
use App\Form\TipoAmortizType;
use App\ClassPrivate\json_lstTipoAmortiz;
use App\ClassPrivate\AmortizProcess;
use Doctrine\Persistence\Event\ManagerEventArgs;



class DefinirController extends AbstractController
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
     * @Route("/activofijo/definir", name="app_tipo_amortiz")
     */
    public function ActivoFijo(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/definir/lst_tipo_amortiz.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }
    /**
     * @Route("/activofijo/definir/list", name="ajax_lstTipoAmortiz")
     */
    public function ajaxListTipoAmortiz(Request $request): Response
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

        $object = new json_lstTipoAmortiz($searchDatos,$pageLong,$pageStart,$arrOrder);

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

    /* Definir la amortizacion de un activo fijo */
    /**
     * @Route("/activofijo/definir/new/{idCode}", name="define_tipo_amortif")
     */    
    public function defineTipoAmortiz(string $idCode,Request $request):Response
    {

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();
        $activoFijo = $em->getRepository(Activofijo::class)->find($idCode);

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

        /* Se esta definiendo la amortización  */
        $entidad = new TipoAmortiz();

        //$entidad->setIdAf($activoFijo->getIdAf());
        
        $frmData = $this->FormularioTipoAmortiz($entidad,"define",$idCode);

        return $this->render('views/definir/new_tipo_amortiz.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode,'datosAF'=>$datosActivoF
        ]);
    }

    /* Permite modificar la informacion */
    /**
     * @Route("/activofijo/definir/write/{idCode}",name="write_tipo_amortiz")
     */
    public function writeTipoAmortiz(string $idCode,Request $request):Response
    {
        //$objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();
        $activoFijo = $em->getRepository(Activofijo::class)->find($idCode);
        $datosActivoF = array(
            'id_af'         =>$activoFijo->getIdAf(),
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


        $entidad = new TipoAmortiz();

        $frmData = $this->FormularioTipoAmortiz($entidad,"define",$idCode);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $this->doctrine->getManager();

            $dataGet = $frmData->getData();
            if (empty($dataGet->getValorSalvamento())){
                $dataGet->setValorSalvamento(0.00);
            }

            if (empty($dataGet->getObserv())){
                $dataGet->setObserv("");
            }
            $dataGet->setIdTipom("");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_tipo_amortiz');
    
        } else {
    
            return $this->render('views/activofijo/new_tipo_amortiz.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode,'datosAF'=>$datosActivoF
            ]);

        }
    }    
    /* Cambiar la amortizacion de un activo fijo */
    /**
     * @Route("/activofijo/definir/edit/{idCode}", name="cambiar_tipo_amortif")
     */    
    public function cambiarTipoAmortiz(string $idCode,Request $request):Response
    {

        //$objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        /* Se esta definiendo la amortización  */
        $entidad = $em->getRepository(TipoAmortiz::class)->find($idCode);

        $activoFijo = $em->getRepository(Activofijo::class)->find($entidad->getIdAf());

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

        //$entidad->setIdAf($activoFijo->getIdAf());
        
        $frmData = $this->FormularioTipoAmortiz($entidad,"edit",$idCode);

        return $this->render('views/definir/edit_tipo_amortiz.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode,'datosAF'=>$datosActivoF
        ]);
    }

    /* Permite modificar la informacion */
    /**
     * @Route("/activofijo/definir/update/{idCode}",name="update_tipo_amortiz")
     */
    public function updateTipoAmortiz(string $idCode,Request $request):Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(TipoAmortiz::class)->find($idCode);


        $activoFijo = $em->getRepository(Activofijo::class)->find($entidad->getIdAf());
        $datosActivoF = array(
            'id_af'         =>$activoFijo->getIdAf(),
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

        $frmData = $this->FormularioTipoAmortiz($entidad,"edit",$idCode);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $this->doctrine->getManager();

            $dataGet = $frmData->getData();

            if (empty($dataGet->getValorSalvamento())){
                $dataGet->setValorSalvamento(0.00);
            }

            if (empty($dataGet->getObserv())){
                $dataGet->setObserv("");
            }            
            //$dataGet->setIdTipom("");

            $em->persist($entidad);
            $em->flush();

            $del_amortiz = $em->getRepository(Amortizaciones::class)->findBy(['id_af' => $dataGet->getIdAf()]); 

            foreach($del_amortiz AS $fila){
                $em->remove($fila);
                $em->flush();
            }

            return $this->redirectToRoute('app_tipo_amortiz');
    
        } else {
    
            return $this->render('views/activofijo/edit_tipo_amortiz.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode,'datosAF'=>$datosActivoF
            ]);

        }
    }    
    /* Controla el Formulario  */
    private function FormularioTipoAmortiz(TipoAmortiz $entity, string $tipo,string $idCode)
    {
        if ($tipo=="define") {
            $form=$this->createForm(TipoAmortizType::class,$entity,array(
                'action'=> $this->generateUrl('write_tipo_amortiz',['idCode'=>$idCode]),
                'method'=>'POST',
                'process'=>'define',
                 ));
            
        } else {
            $form = $this->createForm(TipoAmortizType::class,$entity,array(
                    'action'=>$this->generateUrl('update_tipo_amortiz',['idCode'=>$entity->getIdTipom()]),
                    'method'=>'POST',
                    'process'=>'edit',
                ));
        }
        return $form;
    }

    /* Permite modificar la informacion */
    /**
     * @Route("/activofijo/amortizacion/calculo",name="calculo_tipo_amortiz")
     */
    public function calculoTipoAmortiz(Request $request):Response
    {
        if ($request->isMethod('POST')) {    
            $idUbic = $request->get('id_ubic');
        }
        $data = array();
        $this->doctrine->getConnection()->beginTransaction();
        try{
           
            $objAmortiz = new AmortizProcess($this->doctrine);

            $objAmortiz->setIdUbicacion($idUbic);
            $objAmortiz->setFechaActual(new \DateTime());

            if ($objAmortiz->generarAmortizaciones()!=true){
                $data['ok']='15';
                $data['msg']=$objAmortiz->getErrorMensaje();
                $this->doctrine->getConnection()->rollback();
            } else {
                $data['ok']='01';
                $data['msg']='Correct!!';
                $this->doctrine->getConnection()->commit();
            }
        } catch(\Exception $err){
            $data['ok'] = '35';
            $data['msg'] = 'Error:'.$err->getMessage().' '.$err->getLine();
            
            $this->doctrine->getConnection()->rollback();
        }
        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;   
    }    
}