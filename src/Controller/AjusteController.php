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
use App\Entity\IndicePrecio;
use App\Entity\PermisoMenu;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\IndicePrecioType;
use App\ClassPrivate\json_lstIndicePrecio;

class AjusteController extends AbstractController
{
    /**
     * @Route("/indicePrecio", name="app_indicePrecio")
     */
    public function IndicePrecio(ManagerRegistry $doctrine): Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/indicePrecio/lst_indice_precio.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/indicePrecio/list", name="ajax_lstIndicePrecio")
     */
    public function ajaxListIndicePrecio(Request $request,ManagerRegistry $doctrine,globalFunc $funcGlobal ): Response
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

        $object = new json_lstIndicePrecio($searchDatos,$pageLong,$pageStart,$arrOrder);

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
     * @Route("/indicePrecio/new", name="new_indicePrecio")
     */    
    public function newIndicePrecio(Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new IndicePrecio();

        $frmData = $this->FormularioIndicePrecio($entidad,"new");

        return $this->render('views/indicePrecio/new_indice_precio.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
        ]);
    }
    /* Permite grabar la informacion del FormularioIndicePrecio */
    /**
     * @Route("/indicePrecio/write", name="write_indicePrecio")
     */
    public function writeIndicePrecio(Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $entidad = new IndicePrecio();
        $frmData = $this->FormularioIndicePrecio($entidad,"new");
        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $dataEntity = $frmData->getData();

            $dataEntity->setIdIpc("");

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_indicePrecio');
    
        } else {
    
            return $this->render('views/indicePrecio/new_indice_precio.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView()
            ]);

        }
    }    

    /* Permite modificar un IndicePrecio */
    /**
     * @Route("/indicePrecio/edit/{idCode}", name="edit_indicePrecio")
     */    
    public function editIndicePrecio(string $idCode,Request $request, ManagerRegistry $doctrine):Response
    {

        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();

        $entidad = $em->getRepository(IndicePrecio::class)->find($idCode);

        $frmData = $this->FormularioIndicePrecio($entidad,"edit");

        return $this->render('views/indicePrecio/edit_indice_precio.html.twig', [
            'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(), 'idCode'=>$idCode
        ]);
    }

    /**
     * @Route("/indicePrecio/update/{idCode}",name="update_indicePrecio")
     */
    public function updateIndicePrecio(string $idCode,Request $request,ManagerRegistry $doctrine):Response
    {
        $objFunc = new globalFunc($doctrine);
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $doctrine->getManager();
        $entidad = $em->getRepository(IndicePrecio::class)->find($idCode);

        //$entidad->setIdPropiedad($idCode);

        $frmData = $this->FormularioIndicePrecio($entidad,"edit");

        $frmData->handleRequest($request);

        if ($frmData->isSubmitted() && $frmData->isValid()){
            /*  Se Guarda la información */
            $em = $doctrine->getManager();

            $em->persist($entidad);
            $em->flush();

            return $this->redirectToRoute('app_indicePrecio');
    
        } else {
    
            return $this->render('views/indicePrecio/edit_indice_precio.html.twig', [
                'arrMenu'=>$arrMenu, 'form' =>$frmData->createView(),'idCode'=>$idCode
            ]);

        }
    }    

    /* Permite eliminar un indice precio */
    /**
     * @Route("/indicePrecio/delete",name="delete_indicePrecio")
     */
    public function deleteIndicePrecio(Request $request,ManagerRegistry $doctrine):response
    {
        if ($request->isMethod('POST')) {
            $idCode = $request->get('idCode');
        } else {
            $idCode = "";
        }
        $data = array();

        try {
            $em = $doctrine->getManager();
            
            $entidad = $em->getRepository(IndicePrecio::class)->find($idCode);

            $em->remove($entidad);

            if ($em->flush()){
                $data['ok']='33';
                $data['msg']='No se permitio la eliminación del IPC';
            } else {
                $data['ok']='01';
                $data['msg']='Se elimino correctamente el Indice Precio';
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
    private function FormularioIndicePrecio(IndicePrecio $entity, string $tipo)
    {
        if ($tipo=="new") {
            $form=$this->createForm(IndicePrecioType::class,$entity,array(
                'action'=> $this->generateUrl('write_indicePrecio'),
                'method'=>'POST',
                'process'=>'new',
                 ));
            
        } else {
            $form = $this->createForm(IndicePrecioType::class,$entity,array(
                    'action'=>$this->generateUrl('update_indicePrecio',['idCode'=>$entity->getIdIpc()]),
                    'method'=>'POST',
                    'process'=>'edit',
                       ));
        }
        return $form;
    }
    
}
