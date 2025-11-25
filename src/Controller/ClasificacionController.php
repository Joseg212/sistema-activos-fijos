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
use App\Entity\Clasificacion;
use App\Entity\PermisoMenu;
use App\Form\ClasificacionType;
use App\ClassPrivate\json_lstClasificacion;

class ClasificacionController extends AbstractController
{
    /**
     * @Route("/Clasificacion", name="app_clasificacion")
     */
    public function Clasificacion(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/Clasificacion/lst_clasificacion.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/Clasificacion/list", name="ajax_lstClasificacion")
     */
    public function ajaxListClasificacion(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
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

        $object = new json_lstClasificacion($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setFuncGlobal($funcGlobal);
        $object->dataSearch($typeSearch,$txtSearch);

        $data = array();
        
        $data = $object->responseData($em);

        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }

    /* Permite agregar una Clasificacion */
    /**
     * @Route("/Clasificacion/new", name="new_clasificacion")
     */    
    public function newClasificacion(Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new Clasificacion();

        $frmData = $this->FormularioClasificacion($entidad,"new");

        return $this->render('views/Clasificacion/new_clasificacion.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
        ]);
    }
    /* Permite grabar la informacion del FormularioClasificacion */
    /**
     * @Route("/Clasificacion/write", name="write_clasificacion")
     */
    public function writeClasificacion(Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new Clasificacion();
        $frmData = $this->FormularioClasificacion($entidad,"new");
        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataEntity = $frmData->getData();

            $dataEntity->setIdClase("");

            if (empty($dataEntity->getObservacion())){
                $dataEntity->setObservacion("");
            }
            if (empty($dataEntity->getCodCuenta())){
                $dataEntity->setCodCuenta("");
            }

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_clasificacion');
    
        } else {
    
            return $this->render('views/Clasificacion/new_clasificacion.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
            ]);

        }
    }    

    /* Permite modificar un Clasificacion */
    /**
     * @Route("/Clasificacion/edit/{idCode}", name="edit_clasificacion")
     */    
    public function editClasificacion(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();

        $entidad = $em->getRepository(Clasificacion::class)->find($idCode);

        $frmData = $this->FormularioClasificacion($entidad,"edit");

        return $this->render('views/Clasificacion/edit_clasificacion.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /**
     * @Route("/Clasificacion/update/{idCode}",name="update_clasificacion")
     */
    public function updateClasificacion(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(Clasificacion::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioClasificacion($entidad,"edit");

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_clasificacion');
    
        } else {
    
            return $this->render('views/Clasificacion/edit_clasificacion.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite eliminar una Clasificacion */
    /**
     * @Route("/Clasificacion/delete",name="delete_clasificacion")
     */
    public function deleteClasificacion(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(Clasificacion::class)->find($idCode);

            $em->remove($entidad);

            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación de la Clasificación';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente la Clasificación';
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
    private function FormularioClasificacion(Clasificacion $entity, string $tipo)
    {
        if ($tipo=="new") {
            $form=$this->createForm(ClasificacionType::class,$entity,array(
                'action'=> $this->generateUrl('write_clasificacion'),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } else {
            $form = $this->createForm(ClasificacionType::class,$entity,array(
                    'action'=>$this->generateUrl('update_clasificacion',['idCode'=>$entity->getIdClase()]),
                    'method'=>'POST',
                    'process'=>'edit',
                       ));
        }
        return $form;
    }
    /**
     * @Route("/Clasificacion/ajax/selClase", name="ajax_sel_clase")
     */
    public function ajaxSelClase(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        
        $data = array();

        $query = $em->getRepository(Clasificacion::class)->findByClases();

        $data['elements']=array();

        $data['elements'][] = array(
            "id"=>"todos",
            "data"=>"Todos"
        );

        if ($query){
            $result =  $query->getResult();
            foreach($result as $row){
                $data['elements'][] = array(
                    "id"=>$row['id_clase'],
                    "data"=>$row['descripcion'],
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

}
