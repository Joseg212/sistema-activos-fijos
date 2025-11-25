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
use App\Entity\PermisoMenu;
use App\ClassPrivate\json_lstIdentificar;
use App\ClassPrivate\genReportJasper;
use App\Entity\FactMejora;
use App\Entity\FactMejoratmp;
use Doctrine\Persistence\Event\ManagerEventArgs;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class IdentificarController extends AbstractController
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
     * @Route("/identificar/listado", name="app_identificar")
     */
    public function indexIdentificar(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/identificar/lst_identificar.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    }

    /**
     * @Route("/identificar/lista/activos", name="ajax_lstIdentificar")
     */
    public function ajaxListActivoFijo(Request $request): Response
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

        $object = new json_lstIdentificar($searchDatos,$pageLong,$pageStart,$arrOrder);

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
     * @Route("/identificar/activos/marca", name="mark_identificar")
     */
    public function ActivosMarca(Request $request):response
    {
        if ($request->isMethod('POST')) {
            $condi = $request->get('condi');
            $idCode= $request->get('idCode');
        } else {
            $condi = "not";
            $idCode= "";
        }
        $data = array();
        try {
            //Encontrar el Activo
            $em = $this->doctrine->getManager();

            $activo = $em->getRepository(Activofijo::class)->find($idCode);

            // Si no tiene el code asignado se genera
            $data['refresh']='not';
            if ($activo->getCodeActivof()==""){
                $numero =(integer)mt_rand(10000000,9999999999);
                $activo->setCodeActivof((string)$numero);
                $data['refresh']='yes';
            };
            if ($condi=='yes'){
                $activo->setMarca(1);
            } else {
                $activo->setMarca(0);
            }

            $em->persist($activo);
            $em->flush();
        } catch (\Exception $err) {
            $data['ok']='22';
            $data['msg']='Error:'.$err->getMessage().'\n'.$err->getLine();
        } finally {
            $data['ok']='01';
            $data['msg']='Completo correctamente!!';
        }
        ob_clean();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;    
    }
    /**
     * @Route("/identificar/activo/reporte/etiqueta",name="tags_identificar")
     */
    public function tagsIdentificar():response
    {

        $rootProject = $this->getParameter('kernel.project_dir'); 

        $jasperFileName = $rootProject."\\public\\reports\\generated\\rpt_etiquetas.jasper";

        $pdfFileName = $rootProject."\\public\\reports\\created\\etiquetas_".date("Ymd_Hms");

        $reportObj = new genReportJasper($jasperFileName,$pdfFileName,['receive'=>'texto']);

        $reportObj->runReport();

        return (new BinaryFileResponse($pdfFileName.".pdf", Response::HTTP_OK))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'etiquetas_'.date("Ymd_Hms").'.pdf');        
    }

}
