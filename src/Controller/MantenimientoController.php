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
use App\Entity\Mantenimiento;
use App\Entity\PermisoMenu;
use App\Entity\Responsable;
use App\Form\MantenimientoType;
use App\ClassPrivate\json_lstReparacion;
use App\ClassPrivate\json_lstMejora;
use App\ClassPrivate\json_lstFactMejora;
use App\Entity\FactMejora;
use App\Entity\FactMejoratmp;
use Doctrine\Persistence\Event\ManagerEventArgs;



class MantenimientoController extends AbstractController
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
     * @Route("/mantenimiento/lista/activos/rep", name="app_sel_activo_rep")
     */
    public function lstSelActivoRep(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/mantenimiento/lst_sel_activo_mant.html.twig', [
            'arrMenu'=>$arrMenu, 'typeSelect'=>'rep'
        ]);
    }

    /**
     * @Route("/mantenimiento/lista/activos/mejora", name="app_sel_activo_mej")
     */
    public function lstSelActivoMej(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/mantenimiento/lst_sel_activo_mant.html.twig', [
            'arrMenu'=>$arrMenu, 'typeSelect'=>'mejora'
        ]);
    }

    /**
     * @Route("/mantenimiento/reparacion/listado/{idActivo}", name="app_reparacion")
     */
    public function lstSelActivos(string $idActivo): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/mantenimiento/lst_reparacion.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo
        ]);
    }

    /**
     * @Route("/mantenimiento/reparacion/list", name="ajax_lstReparacion")
     */
    public function ajaxListReparacion(Request $request): Response
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
            $idActivo = $request->get('idActivo');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $idActivo = '';
        }

        $rootWeb = $this->funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$this->doctrine->getManager();

        $object = new json_lstReparacion($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdActivo($idActivo);
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
     * @Route("/mantenimiento/reparacion/new/{idActivo}", name="new_reparacion")
     */
    public function newReparacion(string $idActivo): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = new Mantenimiento();
        $frmData = $this->FormularioMant($entidad,"new",$idActivo);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        return $this->render('views/mantenimiento/new_reparacion.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF, 'form'=> $frmData->createView()
        ]);
    }

    /**
     * @Route("/mantenimiento/reparacion/write/{idActivo}", name="write_reparacion")
     */
    public function writeReparacion(string $idActivo,Request $request): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = new Mantenimiento();
        $frmData = $this->FormularioMant($entidad,"new",$idActivo);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */

            $reparacion = $frmData->getData();
            
            $reparacion->setIdMant("");

            if (empty($reparacion->getSiTraslado())){
                $reparacion->setSiTraslado(0);
            }
            if (empty($reparacion->getTelefonoProv())){
                $reparacion->setTelefonoProv("");
            }
            if (empty($reparacion->getSiTraslado())){
                $reparacion->setSiTraslado(0);
            }
            if (empty($reparacion->getCostoTraslado()) && $reparacion->getSiTraslado()==0){
                $reparacion->setCostoTraslado(0.00);
                $reparacion->setImpTraslado(0.00);
                $reparacion->setTotalTraslado(0.00);
            }
            $reparacion->setTipoMant("Reparación");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_reparacion',['idActivo'=>$idActivo]);

        } else {
            return $this->render('views/mantenimiento/new_reparacion.html.twig', [
                'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF,'form' => $frmData->createView()
            ]);
    
        }
    }

    /**
     * @Route("/mantenimiento/reparacion/edit/{idCode}", name="edit_reparacion")
     */
    public function editReparacion(string $idCode): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

        $idActivo = $entidad->getIdAf();

        $frmData = $this->FormularioMant($entidad,"edit",$idCode);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        $datosResp = $em->getRepository(Responsable::class)->findOneByDataResp($entidad->getIdResp());

        return $this->render('views/mantenimiento/edit_reparacion.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF, 'form'=> $frmData->createView(),'dataResp'=>$datosResp
        ]);
    }
    /**
     * @Route("/mantenimiento/reparacion/update/{idCode}", name="update_reparacion")
     */
    public function updateReparacion(string $idCode,Request $request): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

        $idActivo = $entidad->getIdAf();

        $frmData = $this->FormularioMant($entidad,"edit",$idCode);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);
        $datosResp = $em->getRepository(Responsable::class)->findOneByDataResp($entidad->getIdResp());

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */

            $reparacion = $frmData->getData();

            if (empty($reparacion->getTelefonoProv())){
                $reparacion->setTelefonoProv("");
            }
            if (empty($reparacion->getSiTraslado())){
                $reparacion->setSiTraslado(0);
            }
            if (empty($reparacion->getCostoTraslado()) && $reparacion->getSiTraslado()==0){
                $reparacion->setCostoTraslado(0.00);
                $reparacion->setImpTraslado(0.00);
                $reparacion->setTotalTraslado(0.00);
            }

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_reparacion',['idActivo'=>$idActivo]);

        } else {
            return $this->render('views/mantenimiento/edit_reparacion.html.twig', [
                'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF,'form' => $frmData->createView(),'dataResp'=>$datosResp
            ]);
    
        }
    }

    /**
     * @Route("/mantenimiento/reparacion/delete/",name="delete_reparacion")
     */
    public function deleteReparacion(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();

            $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

            $em->remove($entidad);
            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se pudo eliminar la reparación';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la reparación';
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
     * @Route("/mantenimiento/mejora/listado/{idActivo}", name="app_mejora")
     */
    public function lstMejora(string $idActivo): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $session = $this->requestStack->getSession();

        if ($session->get("costo_fact")!==null){
            $session->remove("costo_fact");
            $session->remove("imp_fact");
            $session->remove("total_fact");
        }
        if ($session->get("UID_d")!==null){
            $session->remove("UID_d");
        }

        return $this->render('views/mantenimiento/lst_mejora.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo
        ]);
    }

    /**
     * @Route("/mantenimiento/mejora/list", name="ajax_lstMejora")
     */
    public function ajaxListMejora(Request $request): Response
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
            $idActivo = $request->get('idActivo');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $idActivo = '';
        }

        $rootWeb = $this->funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$this->doctrine->getManager();

        $object = new json_lstMejora($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdActivo($idActivo);
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
     * @Route("/mantenimiento/mejora/new/{idActivo}", name="new_mejora")
     */
    public function newMejora(string $idActivo): Response
    {
        $session = $this->requestStack->getSession();

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = new Mantenimiento();
        $frmData = $this->FormularioMant($entidad,"mejNew",$idActivo);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        $idMant = $this->generateUID();

        if (null !== $session->get("UID_d")) 
        {
            $idMant = $session->get("UID_d");
            if ($session->get("costo_fact")!==null){
                $costoFact=number_format($session->get("costo_fact"),2,'.',',');
                $impFact=number_format($session->get("imp_fact"),2,'.',',');
                $totalFact=number_format($session->get("total_fact"),2,'.',',');
            } else {
                $costoFact="0.00";
                $impFact="0.00";
                $totalFact="0.00";
            }
        } else {
            $session->set("UID_d",$idMant);
            $costoFact="0.00";
            $impFact="0.00";
            $totalFact="0.00";
        }

        return $this->render('views/mantenimiento/new_mejora.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF, 'form'=> $frmData->createView(),
            'idMant'=>$idMant, 'costoFact'=>$costoFact,'impFact'=>$impFact,'totalFact'=>$totalFact
        ]);
    }
    /**
     * @Route("/mantenimiento/mejora/write/{idActivo}", name="write_mejora")
     */
    public function writeMejora(string $idActivo,Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $idMant = $session->get("UID_d");

        $entidad = new Mantenimiento();


        $frmData = $this->FormularioMant($entidad,"mejNew",$idActivo);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        $frmData->handleRequest($request);


        if ($frmData->isSubmitted() && $frmData->isValid()){
            $this->doctrine->getConnection()->beginTransaction();
            try {
                /*  Se Guarda la información */
                $mejora = $frmData->getData();

                $mejora->setIdMant("");
                $mejora->setProveedor($idMant);
                $mejora->setProveedorRif("");
                $mejora->setUnidadTiempo("Horas");
                $mejora->setNumeroTiempo(0);
                $mejora->setTelefonoProv("");        
                $mejora->setNroFact("");
        
                if (empty($mejora->getSiTraslado())){
                    $mejora->setSiTraslado(0);
                }
                if (empty($mejora->getTelefonoProv())){
                    $mejora->setTelefonoProv("");
                }
                if (empty($mejora->getSiTraslado())){
                    $mejora->setSiTraslado(0);
                }
                if (empty($mejora->getCostoTraslado()) && $mejora->getSiTraslado()==0){
                    $mejora->setCostoTraslado(0.00);
                    $mejora->setImpTraslado(0.00);
                    $mejora->setTotalTraslado(0.00);
                }
                $mejora->setTipoMant("Mejora");

                $em->persist($entidad);
                $em->flush();

                $mejora = $em->getRepository(Mantenimiento::class)->findOneBy(['proveedor'=>$idMant]);

                $id_mant =  $mejora->getIdMant();

                $factMejoras = $em->getRepository(FactMejoratmp::class)->findBy(['id_mant'=>$idMant]);

                foreach($factMejoras As $detalle){
                    $newDetalle = new FactMejora();
                    $newDetalle->setIdDet("");
                    $newDetalle->setIdMant($id_mant);
                    $newDetalle->setProveedor($detalle->getProveedor());
                    $newDetalle->setProveedorRif($detalle->getProveedorRif());
                    $newDetalle->setFechaFact($detalle->getFechaFact());
                    $newDetalle->setNroFact($detalle->getNroFact());
                    $newDetalle->setTelefonoProv($detalle->getTelefonoProv());
                    $newDetalle->setCostoFact($detalle->getCostoFact());
                    $newDetalle->setImpFact($detalle->getImpFact());
                    $newDetalle->setTotalFact($detalle->getTotalFact());
                    $newDetalle->setDetalle($detalle->getDetalle());
                    $newDetalle->setEstatus("A");

                    $em->persist($newDetalle);
                    $em->flush();

                    $em->remove($detalle);
                    $em->flush();
                }
                $this->doctrine->getConnection()->commit();
            } catch (\Exception $err) {
                throw $this->createNotFoundException($err->getMessage()." ". $err->getLine());
                $this->doctrine->getConnection()->rollback();
            }

            $session->remove("costo_fact");
            $session->remove("imp_fact");
            $session->remove("total_fact");
            $session->remove("UID_d");

            return $this->redirectToRoute('app_mejora',['idActivo'=>$idActivo]);

        } else {
            if ($session->get("costo_fact")==null){
                $session->set("costo_fact",0.00);
                $session->set("imp_fact",0.00);
                $session->set("total_fact",0.00);
            }
            return $this->render('views/mantenimiento/new_mejora.html.twig', [
                'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF,'form' => $frmData->createView(),
                'idMant'=>$idMant,'costoFact'=>$session->get("costo_fact"), 'impFact'=>$session->get("imp_fact"),
                'totalFact'=>$session->get("total_fact")
            ]);
        }
    }
    /**
     * @Route("/mantenimiento/mejora/editar/{idCode}", name="edit_mejora")
     */
    public function editMejora(string $idCode): Response
    {
        $session = $this->requestStack->getSession();

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

        $frmData = $this->FormularioMant($entidad,"mejEdit",$idCode);

        $idActivo = $entidad->getIdAf();
        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);
        $datosResp = $em->getRepository(Responsable::class)->findOneByDataResp($entidad->getIdResp());

        $idMant = $idCode;
        if ($session->get("UID_d") == null)
        {
            /* Mover los datos al temporal */ 
            $factMejoraDelete = $em->getRepository(FactMejoratmp::class)->findBy(['id_mant'=>$idMant]);
            foreach($factMejoraDelete As $fila)
            {
                $em->remove($fila);
                $em->flush();
            }

            $factMejoras = $em->getRepository(FactMejora::class)->findBy(['id_mant'=>$idMant]);

            foreach($factMejoras As $detalle){
                $newDetalle = new FactMejoratmp();
                $newDetalle->setIdDet("");
                $newDetalle->setIdMant($idMant);
                $newDetalle->setProveedor($detalle->getProveedor());
                $newDetalle->setProveedorRif($detalle->getProveedorRif());
                $newDetalle->setFechaFact($detalle->getFechaFact());
                $newDetalle->setNroFact($detalle->getNroFact());
                $newDetalle->setTelefonoProv($detalle->getTelefonoProv());
                $newDetalle->setCostoFact($detalle->getCostoFact());
                $newDetalle->setImpFact($detalle->getImpFact());
                $newDetalle->setTotalFact($detalle->getTotalFact());
                $newDetalle->setDetalle($detalle->getDetalle());
                $newDetalle->setEstatus("A");

                $em->persist($newDetalle);
                $em->flush();

            // $em->remove($detalle);
            // $em->flush();
            }        
        }

        if (null !== $session->get("UID_d")) 
        {
            $idMant = $session->get("UID_d");
            if ($session->get("costo_fact")!==null){
                $costoFact=number_format($session->get("costo_fact"),2,'.',',');
                $impFact=number_format($session->get("imp_fact"),2,'.',',');
                $totalFact=number_format($session->get("total_fact"),2,'.',',');
            } else {
                $costoFact=number_format($entidad->getMontoFact(),2,'.',',');
                $impFact=number_format($entidad->getMontoIva(),2,'.',',');
                $totalFact=number_format($entidad->getTotalFactura(),2,'.',',');
            }
        } else {
            $session->set("UID_d",$idMant);
            $session->set("costo_fact",$entidad->getMontoFact());
            $session->set("imp_fact",$entidad->getMontoIva());
            $session->set("total_fact",$entidad->getTotalFactura());

            $costoFact=number_format($entidad->getMontoFact(),2,'.',',');
            $impFact=number_format($entidad->getMontoIva(),2,'.',',');
            $totalFact=number_format($entidad->getTotalFactura(),2,'.',',');
        }

        return $this->render('views/mantenimiento/edit_mejora.html.twig', [
            'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF, 'form'=> $frmData->createView(),
            'idMant'=>$idMant, 'costoFact'=>$costoFact,'impFact'=>$impFact,'totalFact'=>$totalFact,'dataResp'=>$datosResp
        ]);
    }
    /**
     * @Route("/mantenimiento/mejora/actualizar/{idCode}", name="update_mejora")
     */
    public function updateMejora(string $idCode,Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();


        $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

        $idMant = $entidad->getIdMant();

        $idActivo = $entidad->getIdAf();

        $frmData = $this->FormularioMant($entidad,"mejEdit",$idActivo);

        $datosActivoF = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);
        $datosResp = $em->getRepository(Responsable::class)->findOneByDataResp($entidad->getIdResp());

        $frmData->handleRequest($request);


        if ($frmData->isSubmitted() && $frmData->isValid()){
            $this->doctrine->getConnection()->beginTransaction();
            try {
                /*  Se Guarda la información */
                $mejora = $frmData->getData();

                //$mejora->setIdMant("");
                //$mejora->setProveedor($idMant);
                $mejora->setProveedorRif("");
                $mejora->setUnidadTiempo("Horas");
                $mejora->setNumeroTiempo(0);
                $mejora->setTelefonoProv("");        
                $mejora->setNroFact("");
        
                if (empty($mejora->getSiTraslado())){
                    $mejora->setSiTraslado(0);
                }
                if (empty($mejora->getTelefonoProv())){
                    $mejora->setTelefonoProv("");
                }
                if (empty($mejora->getSiTraslado())){
                    $mejora->setSiTraslado(0);
                }
                if (empty($mejora->getCostoTraslado()) && $mejora->getSiTraslado()==0){
                    $mejora->setCostoTraslado(0.00);
                    $mejora->setImpTraslado(0.00);
                    $mejora->setTotalTraslado(0.00);
                }
                //$mejora->setTipoMant("Mejora");

                $em->persist($entidad);
                $em->flush();


                $factMejoraDelete = $em->getRepository(FactMejora::class)->findBy(['id_mant'=>$idMant]);
                foreach($factMejoraDelete As $fila)
                {
                    $em->remove($fila);
                    $em->flush();
                }

                $factMejoras = $em->getRepository(FactMejoratmp::class)->findBy(['id_mant'=>$idMant]);

                foreach($factMejoras As $detalle){
                    $newDetalle = new FactMejora();
                    $newDetalle->setIdDet("");
                    $newDetalle->setIdMant($idMant);
                    $newDetalle->setProveedor($detalle->getProveedor());
                    $newDetalle->setProveedorRif($detalle->getProveedorRif());
                    $newDetalle->setFechaFact($detalle->getFechaFact());
                    $newDetalle->setNroFact($detalle->getNroFact());
                    $newDetalle->setTelefonoProv($detalle->getTelefonoProv());
                    $newDetalle->setCostoFact($detalle->getCostoFact());
                    $newDetalle->setImpFact($detalle->getImpFact());
                    $newDetalle->setTotalFact($detalle->getTotalFact());
                    $newDetalle->setDetalle($detalle->getDetalle());
                    $newDetalle->setEstatus("A");

                    $em->persist($newDetalle);
                    $em->flush();

                    $em->remove($detalle);
                    $em->flush();
                }
                $this->doctrine->getConnection()->commit();
            } catch (\Exception $err) {
                throw $this->createNotFoundException($err->getMessage()." ".$err->getLine());
                $this->doctrine->getConnection()->rollback();
            }

            $session->remove("costo_fact");
            $session->remove("imp_fact");
            $session->remove("total_fact");
            $session->remove("UID_d");

            return $this->redirectToRoute('app_mejora',['idActivo'=>$idActivo]);

        } else {
            if ($session->get("costo_fact")==null){
                $session->set("costo_fact",0.00);
                $session->set("imp_fact",0.00);
                $session->set("total_fact",0.00);
            }
            return $this->render('views/mantenimiento/edit_mejora.html.twig', [
                'arrMenu'=>$arrMenu, 'idActivo' => $idActivo, 'datosAF' => $datosActivoF,'form' => $frmData->createView(),
                'idMant'=>$idMant,'costoFact'=>$session->get("costo_fact"), 'impFact'=>$session->get("imp_fact"),
                'totalFact'=>$session->get("total_fact"), 'dataResp'=>$datosResp
            ]);
        }
    }


    /**
     * @Route("/mantenimiento/mejora/delete/",name="delete_mejora")
     */
    public function deleteMejora(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();

            $detalles=$em->getRepository(FactMejora::class)->findBy(["id_mant"=>$idCode]);

            foreach($detalles AS $fila){
                $em->remove($fila);
                $em->flush();
            }
            
            $entidad = $em->getRepository(Mantenimiento::class)->find($idCode);

            $em->remove($entidad);
            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se pudo eliminar la mejora';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la mejora';
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
     * @Route("/mantenimiento/mejora/ajax/detalle/list", name="ajax_lstFactMejora")
     */
    public function ajaxListFactMejora(Request $request): Response
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
            $idMant = $request->get('idMant');
            $native = $request->get('native');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $idMant = '';
            $native = '';
        }

        $rootWeb = $this->funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$this->doctrine->getManager();

        $object = new json_lstFactMejora($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdMant($idMant);
        $object->setNative($native);
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
     * @Route("/mantenimiento/mejora/ajax/detalle", name="ajax_actionFactMejora")
     */
    public function ajaxActionFactMejora(Request $request): response
    {
        if ($request->isMethod('POST')) {
            //$postDatos =  $this->getRequest()->request->all();
            $action = $request->get('action');
        }
        $session = $this->requestStack->getSession();

        $data = array();

        switch ($action){
            case 'insert':
                $proveedor = $request->get('proveedor');
                $prov_rif = $request->get("prov_rif");
                $fecha_fact = $request->get('fecha_fact');
                $nro_fact = $request->get('nro_fact');
                $telf_prov = $request->get('telf_prov');
                $costo_fact = $request->get('costo_fact');
                $imp_fact = $request->get('imp_fact');
                $total_fact = $request->get('total_fact');
                $detalle = $request->get('detalle');
                $idMant = $request->get('idMant');
    
                $em = $this->doctrine->getManager();

                $entity = new FactMejoratmp();

                $entity->setIdDet("");
                $entity->setIdMant($idMant);
                $entity->setProveedor($proveedor);
                $entity->setProveedorRif($prov_rif);
                $entity->setFechaFact(new \DateTime($fecha_fact));
                $entity->setNroFact($nro_fact);
                if (empty($telf_prov)){
                    $entity->setTelefonoProv("");
                } else {
                    $entity->setTelefonoProv($telf_prov);
                }
                $entity->setDetalle($detalle);
                $entity->setCostoFact($costo_fact);
                $entity->setImpFact($imp_fact);
                $entity->setTotalFact($total_fact);
                $entity->setEstatus("P");

                $em->persist($entity);
                $em->flush();

                if ($session->get("costo_fact")!==null){
                    $costo_fact = $costo_fact + $session->get("costo_fact");
                    $imp_fact = $imp_fact + $session->get("imp_fact");
                    $total_fact = $total_fact + $session->get("total_fact");
                }
                $session->set("costo_fact",$costo_fact);
                $session->set("imp_fact",$imp_fact);
                $session->set("total_fact",$total_fact);

                $data['ok']='01';
                $data['msg']='Correct!!';
                $data['costo_fact'] =number_format($costo_fact,2,'.',',');
                $data['imp_fact'] = number_format($imp_fact,2,'.',',');
                $data['total_fact'] = number_format($total_fact,2,'.',',');

        };

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    

    }

    /* Controla el Formulario  */
    private function FormularioMant(Mantenimiento $entity, string $tipo,string $idActivo)
    {

        if ($tipo=="new") {
            $form=$this->createForm(MantenimientoType::class,$entity,array(
                'action'=> $this->generateUrl('write_reparacion',['idActivo'=>$idActivo]),
                'method'=>'POST',
                'process'=>'new',
                    ));
            
        } elseif ($tipo=="edit") {
            $form = $this->createForm(MantenimientoType::class,$entity,array(
                    'action'=>$this->generateUrl('update_reparacion',['idCode'=>$entity->getIdMant()]),
                    'method'=>'POST',
                    'process'=>'edit',
                ));
        } elseif ($tipo=='mejNew'){
            $form=$this->createForm(MantenimientoType::class,$entity,array(
                'action'=> $this->generateUrl('write_mejora',['idActivo'=>$idActivo]),
                'method'=>'POST',
                'process'=>'mejNew',
                    ));
        } elseif ($tipo=='mejEdit'){
            $form=$this->createForm(MantenimientoType::class,$entity,array(
                'action'=> $this->generateUrl('update_mejora',['idCode'=>$idActivo]),
                'method'=>'POST',
                'process'=>'mejEdit',
                    ));
        }
        return $form;
    } 
    /* Retorna un Id Unico para la foto a guardar */
    private function generateUID()
    {
        return md5(uniqid());
    }

}