<?php

namespace App\Controller;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormView;
use Doctrine\Persistence\ManagerRegistry;
use App\ClassPrivate\json_lstPropiedad;
use App\ClassPrivate\json_lstUbicaciones;
use App\ClassPrivate\json_lstResponsable;
use App\ClassPrivate\globalFunc;
use App\Entity\Propiedad;
use App\Entity\Ubicacion;
use App\Entity\Responsable;
use App\Entity\IndicePrecio;
use App\Entity\PermisoMenu;
use App\Form\PropiedadType;
use App\Form\UbicacionType;
use App\Form\ResponsableType;
use App\Form\IndicePrecioType;

class TablaController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
 
    /**
     * @Route("/propiedad", name="app_propiedad")
     */
    public function Propiedad(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/propiedad/lst_propiedad.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/propiedad/list", name="ajax_lstPropiedad")
     */
    public function ajaxListPropiedad(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
    {
        if ($request->isMethod('POST')) {
            //$postDatos =  $this->getRequest()->request->all();
            //$postDatos = json_decode($request->getContent(), true);

            // $searchDatos = $postDatos['search']['value'];
            $searchDatos =  ($request->get('search'))['value'];
            $pageLong = $request->get('length');
            $pageStart = $request->get('start');
            $arrOrder = $request->get('order');
            $txtSearch = $request->get('txtSearch');
            $typeSearch = $request->get('typeSearch');
            //$tableOrder=$request->get('order');  //$_POST['order'];
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';

        }
        $rootWeb = $funcGlobal->returnPathWeb($request->getBaseURL());


        $em=$doctrine->getManager();

        $object = new json_lstPropiedad($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setFuncGlobal($funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }

    /* Permite agregar un propiedad */
    /**
     * @Route("/propiedad/new", name="new_propiedad")
     */    
    public function newPropiedad(Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new Propiedad();

        $frmData = $this->FormularioPropiedad($entidad,"new");

        return $this->render('views/propiedad/new_propiedad.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
        ]);
    }
    /* Permite grabar la informacion del FormularioPropiedad */
    /**
     * @Route("/propiedad/write", name="write_propiedad")
     */
    public function writePropiedad(Request $request,ManagerRegistry $doctrine):Response
    {
        $entidad = new Propiedad();
        $frmData = $this->FormularioPropiedad($entidad,"new");
        $frmData->handleRequest($request);

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);


        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $propiedad = $frmData->getData();

            $propiedad->setIdPropiedad("");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_propiedad');
    
        } else {
    
            return $this->render('views/propiedad/new_propiedad.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
            ]);

        }
    }    

    /* Permite modificar un propiedad */
    /**
     * @Route("/propiedad/edit/{idCode}", name="edit_propiedad")
     */    
    public function editPropiedad(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();

        $entidad = $em->getRepository(Propiedad::class)->find($idCode);

        $frmData = $this->FormularioPropiedad($entidad,"edit");

        return $this->render('views/propiedad/edit_propiedad.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /* Permite modificar la informacion del FormularioPropiedad */
    /**
     * @Route("/propiedad/update/{idCode}",name="update_propiedad")
     */
    public function updatePropiedad(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Propiedad::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioPropiedad($entidad,"edit");

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $propiedad = $frmData->getData();

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_propiedad');
    
        } else {
    
            return $this->render('views/propiedad/edit_propiedad.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite modificar la informacion del FormularioPropiedad */
    /**
     * @Route("/propiedad/delete",name="delete_propiedad")
     */
    public function deletePropiedad(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Propiedad::class)->find($idCode);

            $em->remove($entidad);
            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación de la propiedad';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la propiedad';
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
    private function FormularioPropiedad(Propiedad $entity, $tipo)
    {
        if ($tipo=="new") {
            $form=$this->createForm(PropiedadType::class,$entity,array(
                'action'=> $this->generateUrl('write_propiedad'),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } else {
            $form = $this->createForm(PropiedadType::class,$entity,array(
                    'action'=>$this->generateUrl('update_propiedad',array('idCode'=>$entity->getIdPropiedad())),
                    'method'=>'POST',
                    'process'=>'edit',
                       ));
        }
        return $form;
    }

    /*Tabla de ubicacion  */
    /**
     * @Route("/ubicacion", name="app_ubicacion")
     */
    public function Ubicacion(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/ubicacion/lst_ubicacion.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }
    /**
     * @Route("/tablas/selProp", name="ajax_sel_prop")
     */
    public function ajaxSelProp(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        
        $data = array();

        $query = $em->getRepository(Propiedad::class)->findByListProp();

        $data['elements']=array();

        $data['elements'][] = array(
            "id"=>"select",
            "data"=>"Seleccione"
        );

        if ($query){
            $result =  $query->getResult();
            foreach($result as $row){
                $data['elements'][] = array(
                    "id"=>$row['id_propiedad'],
                    "data"=>$row['nombre'],
                );
            }
            $data['ok']='01';
            $data['msg']='Correct!!';
        } else {
            $data['ok']='33';
            $data['msg']='No se obtuvieron datos';
        }

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }

    /**
     * @Route("/tablas/selUbic", name="ajax_sel_ubic")
     */
    public function ajaxSelUbic(Request $request, ManagerRegistry $doctrine): Response
    {
        $idProp = $request->get('idProp');
        $em = $doctrine->getManager();
        
        $data = array();

        $query = $em->getRepository(Ubicacion::class)->findByListUbic($idProp);

        $data['elements']=array();

        $data['elements'][] = array(
            "id"=>"select",
            "data"=>"Seleccione"
        );

        if ($query){
            $result =  $query->getResult();
            foreach($result as $row){
                $data['elements'][] = array(
                    "id"=>$row['id_ubic'],
                    "data"=>$row['ubicacion'],
                );
            }
            $data['ok']='01';
            $data['msg']='Correct!!';
        } else {
            $data['ok']='33';
            $data['msg']='No se obtuvieron datos';
        }

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }

    /**
     * @Route("/ubicacion/list", name="ajax_lstUbicaciones")
     */
    public function ajaxListUbicaciones(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
    {
        if ($request->isMethod('POST')) {
            //$postDatos =  $this->getRequest()->request->all();
            $postDatos = json_decode($request->getContent(), true);

            //$searchDatos = $postDatos['search']['value'];
            $searchDatos =  ($request->get('search'))['value'];
            $pageLong = $request->get('length');
            $pageStart = $request->get('start');
            $arrOrder = $request->get('order');
            $txtSearch = $request->get('txtSearch');
            $typeSearch = $request->get('typeSearch');
            $idPropiedad = $request->get('idPropiedad');
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';
            $idPropiedad = '';
        }

        $rootWeb = $funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$doctrine->getManager();

        $object = new json_lstUbicaciones($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdPropiedad($idPropiedad);
        $object->setFuncGlobal($funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }
    /* Permite agregar un propiedad */
    /**
     * @Route("/ubicacion/new/{idPropiedad}", name="new_ubicacion")
     */    
    public function newUbicacion(string $idPropiedad,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new Ubicacion();
        $frmData = $this->FormularioUbicacion($entidad,"new",$idPropiedad);


        return $this->render('views/ubicacion/new_ubicacion.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idPropiedad'=>$idPropiedad
        ]);
    }
    /* Permite grabar la informacion del FormularioPropiedad */
    /**
     * @Route("/ubicacion/write/{idPropiedad}", name="write_ubicacion")
     */
    public function writeUbicacion(string $idPropiedad,Request $request,ManagerRegistry $doctrine):Response
    {
        $entidad = new Ubicacion();
        $frmData = $this->FormularioUbicacion($entidad,"new",$idPropiedad);
        $frmData->handleRequest($request);

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);


        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataGet = $frmData->getData();

            $dataGet->setIdUbic("");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_ubicacion');
    
        } else {
    
            return $this->render('views/ubicacion/new_ubicacion.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idPropiedad'=>$idPropiedad
            ]);

        }
    } 

    /* Permite modificar una ubicacion */
    /**
     * @Route("/ubicacion/edit/{idCode}", name="edit_ubicacion")
     */    
    public function editUbicacion(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();

        $entidad = $em->getRepository(Ubicacion::class)->find($idCode);

        $frmData = $this->FormularioUbicacion($entidad,"edit","");

        return $this->render('views/ubicacion/edit_ubicacion.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /* Permite modificar la informacion del FormularioUbicacion */
    /**
     * @Route("/ubicacion/update/{idCode}",name="update_ubicacion")
     */
    public function updateUbicacion(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Ubicacion::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioUbicacion($entidad,"edit","");

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_ubicacion');
    
        } else {
    
            return $this->render('views/ubicacion/edit_ubicacion.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite eliminar una ubicacion */
    /**
     * @Route("/ubicacion/delete",name="delete_ubicacion")
     */
    public function deleteUbicacion(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Ubicacion::class)->find($idCode);

            $em->remove($entidad);
            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación de la ubicación';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la ubicación';
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
    private function FormularioUbicacion(Ubicacion $entity, string $tipo,string $idPropiedad)
    {
        if ($tipo=="new") {
            $form=$this->createForm(UbicacionType::class,$entity,array(
                'action'=> $this->generateUrl('write_ubicacion',['idPropiedad'=>$idPropiedad]),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } else {
            $form = $this->createForm(UbicacionType::class,$entity,array(
                    'action'=>$this->generateUrl('update_ubicacion',['idCode'=>$entity->getIdUbic()]),
                    'method'=>'POST',
                    'process'=>'edit',
                       ));
        }
        return $form;
    }


    /**
     * @Route("/responsable", name="app_responsable")
     */
    public function Responsable(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/responsable/lst_responsable.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/responsable/list", name="ajax_lstResponsable")
     */
    public function ajaxListResponsable(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
    {
        if ($request->isMethod('POST')) {
            //$postDatos =  $this->getRequest()->request->all();
            $postDatos = json_decode($request->getContent(), true);

            //$searchDatos = $postDatos['search']['value'];
            $searchDatos =  ($request->get('search'))['value'];
            $pageLong = $request->get('length');
            $pageStart = $request->get('start');
            $arrOrder = $request->get('order');
            $txtSearch = $request->get('txtSearch');
            $typeSearch = $request->get('typeSearch');
            //$tableOrder=$request->get('order');  //$_POST['order'];
        } else {
            $searchDatos="";
            $pageLong=10;
            $pageStart=0;
            $arrOrder=array();
            $arrOrder[0]=array('column'=>0,'dir'=>'desc');
            $typeSearch = 0;
            $txtSearch = '';

        }
        $rootWeb = $funcGlobal->returnPathWeb($request->getBaseURL());


        $em=$doctrine->getManager();

        $object = new json_lstResponsable($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setFuncGlobal($funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }

    /* Permite agregar un responsable */
    /**
     * @Route("/responsable/new", name="new_responsable")
     */    
    public function newResponsable(Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new Responsable();

        $frmData = $this->FormularioResponsable($entidad,"new");

        return $this->render('views/responsable/new_responsable.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
        ]);
    }
    /* Permite grabar la informacion del FormularioPropiedad */
    /**
     * @Route("/responsable/write", name="write_responsable")
     */
    public function writeResponble(Request $request,ManagerRegistry $doctrine):Response
    {
        $entidad = new Responsable();
        $frmData = $this->FormularioResponsable($entidad,"new");
        $frmData->handleRequest($request);

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);


        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataEntity = $frmData->getData();

            $dataEntity->setIdResp("");
            $dataEntity->setFechaReg(new \DateTime());
            $dataEntity->setEstatus("Activo");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_responsable');
    
        } else {
    
            return $this->render('views/responsable/new_responsable.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
            ]);

        }
    }    

    /* Permite modificar un responsable */
    /**
     * @Route("/responsable/edit/{idCode}", name="edit_responsable")
     */    
    public function editResponsable(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();

        $entidad = $em->getRepository(Responsable::class)->find($idCode);

        $frmData = $this->FormularioResponsable($entidad,"edit");

        return $this->render('views/responsable/edit_responsable.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /**
     * @Route("/responsable/update/{idCode}",name="update_responsable")
     */
    public function updateResponsable(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
         $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Responsable::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioResponsable($entidad,"edit");

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_responsable');
    
        } else {
    
            return $this->render('views/responsable/edit_responsable.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite eliminar un responsable */
    /**
     * @Route("/responsable/delete",name="delete_responsable")
     */
    public function deleteResponsable(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Responsable::class)->find($idCode);

            $em->remove($entidad);

            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación de la ubicación';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la ubicación';
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
    private function FormularioResponsable(Responsable $entity, string $tipo)
    {
        if ($tipo=="new") {
            $form=$this->createForm(ResponsableType::class,$entity,array(
                'action'=> $this->generateUrl('write_responsable'),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } else {
            $form = $this->createForm(ResponsableType::class,$entity,array(
                    'action'=>$this->generateUrl('update_responsable',['idCode'=>$entity->getIdResp()]),
                    'method'=>'POST',
                    'process'=>'edit',
                       ));
        }
        return $form;
    }
    
}
