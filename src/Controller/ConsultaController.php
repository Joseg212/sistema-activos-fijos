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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Doctrine\Persistence\Event\ManagerEventArgs;
use App\Entity\PermisoMenu;
use App\Entity\Activofijo;
use App\Entity\TipoAmortiz;
use App\Entity\Mantenimiento;
use App\Entity\IndicePrecio;
use App\Entity\Finiquito;
use App\ClassPrivate\globalFunc;
use App\ClassPrivate\json_QueryActivoFijo;
use App\Entity\Amortizaciones;
use Doctrine\Persistence\ManagerRegistry;


class ConsultaController extends AbstractController
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
     * @Route("/consulta/app/listado", name="app_consulta")
     */
    public function listadoConsulta(): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/consulta/query_activofijo.html.twig', [
            'arrMenu'=>$arrMenu
        ]);
    } 
    /**
     * @Route("/consulta/ajax/lista", name="ajax_lstConsulta")
     */
    public function ajaxListaConsulta(Request $request): Response
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
            $idClase = $request->get('idClase');
            $CodeActivof = $request->get('CodeActivof');
            $estatus = $request->get('Estatus');
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
            $idClase = "";
            $CodeActivof = "";
            $estatus = "";
        }

        $rootWeb = $this->funcGlobal->returnPathWeb($request->getBaseURL());

        $em=$this->doctrine->getManager();

        $object = new json_QueryActivoFijo($searchDatos,$pageLong,$pageStart,$arrOrder);

        $object->setIdPropiedad($idPropiedad);
        $object->setIdUbicacion($idUbicacion);
        $object->setIdClase($idClase);
        $object->setCodeActivof($CodeActivof);
        $object->setEstatus($estatus);
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
     * @Route("/consulta/app/info/{idActivo}", name="info_consulta")
     */
    public function infoConsulta(string $idActivo): Response
    {
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $em = $this->doctrine->getManager();

        $consult  = $em->getRepository(Activofijo::class)->findOneByDataActivo($idActivo);

        $consult['tipoAmortiz'] = $em->getRepository(TipoAmortiz::class)->findByTipoAmortiz($idActivo);

        if ($consult['tipoAmortiz']['estado']=="yes"){
            $amortiz = $em->getRepository(Amortizaciones::class)->findByAmortizaciones($idActivo);
        }
        $mant   = $em->getRepository(Mantenimiento::class)->findByGastosActivo($idActivo);

        return $this->render('views/consulta/info_activofijo.html.twig', [
            'arrMenu'=>$arrMenu, 'consult'=>$consult, 'estado_amortiz'=>$amortiz['estado'], 'amortiz'=>$amortiz['calculos'], 'mant'=>$mant
        ]);
    } 
    /**
     * @Route("/consulta/app/amortiz/list", name="app_amortizaciones")
     */
    public function listadoAmortizaciones(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $fechaDesde =new \DateTime($request->get('fechaDesde'));
            $fechaHasta =new \DateTime($request->get('fechaHasta'));
            $idPropiedad = $request->get('idPropiedad');
            $fecha_desde = $request->get('fechaDesde');
            $fecha_hasta = $request->get('fechaHasta');

            $fechaPatrimonio = $fechaHasta->format("d/m/Y");
        } else {

            $fecha_desde = "";
            $fecha_hasta = "";
            $fechaPatrimonio="";
        }
        $resumen = [];
        $amortiz = [];
        $error = "";
        $totalCapital=0.00;
        $totalRAR = 0.0;
        $muestra = 'not';
        if (isset($fechaDesde))
        {
            try {
                //code...
                $em = $this->doctrine->getManager();
    
                $process = $em->getRepository(Amortizaciones::class)->findByAmortizRango($fechaDesde,$fechaHasta,$idPropiedad);

                //throw $this->createNotFoundException("Datos de ".$process['message']);

                if ($process['estado']=='yes'){
                    $idAfAnterior       = $process['calculos'][0]['id_af'];
                    $idClaseAnterior    = "";
    
                    $token = 1;
                    $fechaValid = null;
                    $ipc_cierre = 0.00;
                    $ipc_origen = 0.00;
                    $factor_correc = 0.00;
                    $costo_ajust =0.00;
                    $depr_ajust = 0.00;
                    $ajust_act = 0.00;
                    $ajust_depr = 0.00;
                    $ajust_neto = 0.00;
                    $poscRsm = 0;
        
                    foreach($process['calculos'] as $fila){
        
                        if ($idAfAnterior!=$fila['id_af']){
                            // Carga la informacion 
                            $token = 1;
                        }
                        if ($token==1){
                            $fechaValid = (($fila['fecha']>$fechaDesde) ? $fila['fecha'] : $fechaDesde);
                            $ipc_cierre = $em->getRepository(IndicePrecio::class)->findOneBy(['mes'=>$fechaHasta->format('m'),'anio'=>$fechaHasta->format('Y')]);
                            $ipc_origen = $em->getRepository(IndicePrecio::class)->findOneBy(['mes'=>$fechaValid->format('m'),'anio'=>$fechaValid->format('Y')]);
                            $factor_correc = ($ipc_cierre->getFactor()/$ipc_origen->getFactor());
                            $costo_ajust = ($fila['costo_hist']*$factor_correc);
                            $depr_ajust = ($fila['amortiz_acum']*$factor_correc);
                            $ajust_act = $costo_ajust - $fila['costo_hist'];
                            $ajust_depr = $fila['amortiz_ajust'] - ($fila['amortiz_ajust']/$factor_correc);
                            $ajust_neto = $ajust_act;
        
                            $amortiz[] = [
                                'id_af'         => $fila['id_af'],
                                'descrip'       => $fila['descrip'],
                                'clasific'      => $fila['clasific'],
                                'fecha_orig'    => $fechaValid->format("d/m/Y"),
                                'ipc_cierre'    => number_format($ipc_cierre->getFactor(),6,',','.'),
                                'ipc_origen'    => number_format($ipc_origen->getFactor(),6,',','.'),
                                'factor_correc' => number_format($factor_correc,6,',','.'),
                                'costo_act'     => number_format($fila['costo_hist'],2,',','.'),
                                'costo_ajust'   => number_format($costo_ajust,2,',','.'),
                                'depr_acum'     => number_format(($fila['amortiz_ajust']/$factor_correc),2,',','.'),
                                'depr_ajust'    => number_format($fila['amortiz_ajust'],2,',','.'),
                                'ajuste_act'     => number_format($ajust_act,2,',','.'),
                                'ajuste_depr'    => number_format($ajust_depr,2,',','.'),
                                'ajuste_neto'    => number_format($ajust_neto,2,',','.'),
                                'tasa_raa'      => number_format(($ajust_neto*0.03),2,',','.'),
                            ];
                            $token = 0;
                            $idAfAnterior=$fila['id_af'];
                            $totalCapital+=$ajust_neto;
                            $totalRAR+=($ajust_neto*0.03);
                            // Se va ingresando el acumulado
                            if ($idClaseAnterior==$fila['id_clase']){
                                $resumen[$poscRsm]['ajuste_act']+=$ajust_act;
                                $resumen[$poscRsm]['ajuste_depr']+=$ajust_depr;
                                $resumen[$poscRsm]['ajuste_neto']+=$ajust_neto;
                                $resumen[$poscRsm]['tasa_raa']+=($ajust_neto*0.03);
                                $resumen[$poscRsm]['depr_neta']+=$fila['amortiz_ajust'];
                                $resumen[$poscRsm]['depr_acum']+=($fila['amortiz_ajust']/$factor_correc);
                                $resumen[$poscRsm]['depr_ajust']+=$fila['amortiz_ajust'];

                                $resumen[1]['debe']+=($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_act);
                                $resumen[1]['haber']+=($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_neto);
                        } else {
                                $idClaseAnterior = $fila['id_clase'];
                                $poscRsm++;
                                if ($poscRsm>1){
                                    $resumen[1]['debe']+=($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_act);
                                    $resumen[1]['haber']+=($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_neto);
                                }

                                $resumen[$poscRsm] = [
                                    'id_af'         => $fila['id_af'],
                                    'descrip'       => $fila['descrip'],
                                    'clasific'      => $fila['clasific'],
                                    'fecha_orig'    => $fechaValid->format("d/m/Y"),
                                    'ipc_cierre'    => "0.00",
                                    'ipc_origen'    => "0.00",
                                    'factor_correc' => "0.00",
                                    'costo_act'     => "0.00",
                                    'costo_ajust'   => "0.00",
                                    'depr_acum'     => ($fila['amortiz_ajust']/$factor_correc),
                                    'depr_ajust'    => $fila['amortiz_ajust'],
                                    'ajuste_act'    => $ajust_act,
                                    'ajuste_depr'   => $ajust_depr,
                                    'ajuste_neto'   => $ajust_neto,
                                    'tasa_raa'      => ($ajust_neto*0.03),
                                    'depr_neta'     => $fila['amortiz_ajust'],
                                    'debe'          => ($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_act),
                                    'haber'         => ($fila['amortiz_ajust']+($ajust_neto*0.03)+$ajust_neto),
                                ];
                            } // if resumen
                        } // if token 
                    } // endfor
                    // Enmascarar los datos mediante number format.
                    for ($i=1;$i<=$poscRsm;$i++){
                        $resumen[$i]['depr_acum'] = number_format($resumen[$i]['depr_acum'],2,',','.');
                        $resumen[$i]['depr_ajust'] = number_format($resumen[$i]['depr_ajust'],2,',','.');
                        $resumen[$i]['ajuste_act'] = number_format($resumen[$i]['ajuste_act'],2,',','.');
                        $resumen[$i]['ajuste_depr'] = number_format($resumen[$i]['ajuste_depr'],2,',','.');
                        $resumen[$i]['ajuste_neto'] = number_format($resumen[$i]['ajuste_neto'],2,',','.');
                        $resumen[$i]['tasa_raa'] = number_format($resumen[$i]['tasa_raa'],2,',','.');
                        $resumen[$i]['depr_neta'] = number_format($resumen[$i]['depr_neta'],2,',','.');
                    }
                    $resumen[1]['debe'] = number_format($resumen[1]['debe'],2,',','.');
                    $resumen[1]['haber'] = number_format($resumen[1]['haber'],2,',','.');
                    $muestra="yes";
                }
            } catch (\Exception $err) {
                //throw $th;
                $error = $err->getMessage() .' Line Erro:'.$err->getLine().' Program:';
                //throw $this->createNotFoundException("Datos de ".$error);
            }
        }

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/consulta/query_amortizaciones.html.twig', [
            'arrMenu'=>$arrMenu, 'muestra'=> $muestra, 'resumen'=>$resumen,'amortiz'=>$amortiz, 'error'=>$error,
            'fecha_desde'=>$fecha_desde, 'fecha_hasta'=>$fecha_hasta,'ajusteCapital' => number_format($totalCapital,2,',','.'),
            'fechaPatrimonio' => $fechaPatrimonio,'tasaRAR'=>number_format($totalRAR,2,',','.')
        ]);
    } 

    /**
     * @Route("/consulta/app/gastos/af/list", name="app_gastos_af")
     */
    public function listadoGastosAf(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $fechaDesde =new \DateTime($request->get('fechaDesde'));
            $fechaHasta =new \DateTime($request->get('fechaHasta'));
            $idPropiedad = $request->get('idPropiedad');
            $fecha_desde = $request->get('fechaDesde');
            $fecha_hasta = $request->get('fechaHasta');

        } else {

            $fecha_desde = "";
            $fecha_hasta = "";
        }
        $resumen = [];
        $gastosAf = [];
        $error = "";
        $muestra = 'not';
        $simejora = "not";
        $totalDebe = 0.00;
        $totalHaber = 0.00;
        if (isset($fechaDesde))
        {
            try {
                //code...
                $em = $this->doctrine->getManager();
    
                $process = $em->getRepository(Mantenimiento::class)->findByGastosRango($fechaDesde,$fechaHasta,$idPropiedad);
                if (isset($process['message'])){
                    throw $this->createNotFoundException("Datos de ".$process['message']);
                }

                if ($process['estado']=='yes'){
                    $idClaseAnterior    = "";
                    $ajust_act = 0.00;
                    $ajust_depr = 0.00;
                    $ajust_neto = 0.00;
                    $poscRsm = 0;
        
                    foreach($process['calculos'] as $fila){
        
                        $gastosAf[] = [
                            'id_mant'          =>   $fila['id_mant'],
                            'id_af'            =>   $fila['id_af'],
                            'id_clase'         =>   $fila['id_clase'],
                            'descrip'          =>   $fila['descrip'],
                            'clasific'         =>   $fila['clasific'],
                            'tipo_mant'        =>   $fila['tipo_mant'],
                            'fecha_fact'       =>   $fila['fecha_fact']->format("d/m/Y"),
                            'numero_fact'      =>   $fila['numero_fact'],
                            'proveedor'        =>   $fila['proveedor'],
                            'prov_rif'         =>   $fila['prov_rif'],
                            'banco'            =>   $fila['banco'],
                            'tipo_doc'         =>   $fila['tipo_doc'],
                            'numero_doc'       =>   $fila['numero_doc'],
                            'costo_fact'       =>   number_format($fila['costo_fact'],2,',','.'),
                            'imp_fact'         =>   number_format($fila['imp_fact'],2,',','.'),
                            'total_fact'       =>   number_format($fila['total_fact'],2,',','.'),
                            'traslado'         =>   [
                                        'si'            => $fila['traslado']['si'],
                                        'costo_tras'    => number_format($fila['traslado']['costo_tras'],2,',','.'),
                                        'imp_tras'      => number_format($fila['traslado']['imp_tras'],2,',','.'),
                                        'total_tras'    => number_format($fila['traslado']['total_tras'],2,',','.'),
                                            ],
                            'unidad_tiempo'    =>   $fila['unidad_tiempo'],
                            'numero_tiempo'    =>   $fila['numero_tiempo'],
                            'detalle'          =>   $fila['detalle'],
                            'total_imp'        =>   number_format(($fila['imp_fact']+ $fila['traslado']['imp_tras']),2,',','.'),
    
                        ];
                        // Se va ingresando el acumulado
                        if ($idClaseAnterior==$fila['id_clase']){
                            $resumen[$poscRsm]['costo_fact']+=$fila['costo_fact'];
                            $resumen[$poscRsm]['imp_fact']+=$fila['imp_fact'];
                            $resumen[$poscRsm]['total_fact']+=$fila['total_fact'];
                            $resumen[$poscRsm]['total_imp']+=($fila['imp_fact']+ $fila['traslado']['imp_tras']);
                            if ($fila['traslado']['si']==1){
                                $resumen[$poscRsm]['si_tras'] = 'yes';

                                $resumen[$poscRsm]['costo_tras']+=$fila['traslado']['costo_tras'];
                                $resumen[$poscRsm]['imp_tras']+=$fila['traslado']['imp_tras'];
                                $resumen[$poscRsm]['total_tras']+=$fila['traslado']['total_tras'];

                                $resumen[1]['debe']+=($fila['traslado']['imp_tras']+$fila['traslado']['costo_tras']);
                                $resumen[1]['haber']+=($fila['traslado']['total_tras']);
    
                            } else {
                                $resumen[$poscRsm]['si_tras'] =($resumen[$poscRsm]['si_tras']=='not') ? 'not' : 'yes';
                            }
                            $resumen[1]['debe']+=($fila['traslado']['imp_tras']+$fila['traslado']['costo_tras']);
                            $resumen[1]['haber']+=($fila['total_fact']);

                        } else {
                            $idClaseAnterior = $fila['id_clase'];
                            $poscRsm++;
                            if ($poscRsm>1){
                                $resumen[1]['debe']+=($fila['traslado']['imp_tras']+$fila['traslado']['costo_tras']);
                                $resumen[1]['haber']+=($fila['total_fact']);
                            }

                            $resumen[$poscRsm] = [
                                    'clasific'      => $fila['clasific'],
                                    'costo_fact'    => $fila['costo_fact'],
                                    'imp_fact'      => $fila['imp_fact'],
                                    'total_fact'    => $fila['total_fact'],
                                    'total_imp'     => ($fila['imp_fact']+ $fila['traslado']['imp_tras']),
                                    'debe'          => ($fila['traslado']['imp_tras']+$fila['traslado']['costo_tras']),
                                    'haber'         => ($fila['total_fact'])
                            ];

                            if ($fila['traslado']['si']==1){
                                $resumen[$poscRsm]['si_tras'] = 'yes';

                                $resumen[$poscRsm]['costo_tras']=$fila['traslado']['costo_tras'];
                                $resumen[$poscRsm]['imp_tras']=$fila['traslado']['imp_tras'];
                                $resumen[$poscRsm]['total_tras']=$fila['traslado']['total_tras'];

                            } else {
                                $resumen[$poscRsm]['si_tras'] ='not';
                            }

                        } // if resumen
                        // Registro en el Mayor Aux
                        if ($fila['tipo_mant']=='Reparación')
                        {
                            if ($fila['traslado']['si']==1){
                                $this->cuentaMayor("102","GASTOS EN TRASLADO",$fila['traslado']['costo_tras'],"D");
                                $this->cuentaMayor("103","RETENCIÓN CRÉDITO FISCAL",$fila['traslado']['imp_tras'],"D");
                                $this->cuentaMayor("201","BANCO",$fila['traslado']['total_tras'],"H");

                                $totalDebe += $fila['traslado']['imp_tras']+$fila['traslado']['costo_tras'];
                                $totalHaber += $fila['traslado']['total_tras'];
                            }
                            $this->cuentaMayor("101","GASTOS EN REPARACIONES",$fila['costo_fact'],"D");
                            $this->cuentaMayor("103","RETENCIÓN CRÉDITO FISCAL",$fila['imp_fact'],"D");
                            $this->cuentaMayor("201","BANCO",$fila['total_fact'],"H");
                            $totalDebe += $fila['imp_fact']+$fila['costo_fact'];
                            $totalHaber += $fila['total_fact'];

                        } elseif($fila['tipo_mant']=='Mejora'){
                            $simejora = "yes";
                        }

                    } // endfor
                    // Enmascarar los datos mediante number format.
                    for ($i=1;$i<=$poscRsm;$i++){
                        $resumen[$i]['costo_fact'] = number_format($resumen[$i]['costo_fact'],2,',','.');
                        $resumen[$i]['imp_fact'] = number_format($resumen[$i]['imp_fact'],2,',','.');
                        $resumen[$i]['total_fact'] = number_format($resumen[$i]['total_fact'],2,',','.');
                        if ($resumen[$i]['total_fact'] == 'yes'){
                            $resumen[$i]['costo_tras'] = number_format($resumen[$i]['costo_tras'],2,',','.');
                            $resumen[$i]['imp_tras'] = number_format($resumen[$i]['imp_tras'],2,',','.');
                            $resumen[$i]['total_tras'] = number_format($resumen[$i]['total_tras'],2,',','.');
                        }
                    }
                    if (isset($resumen[1]['debe'])){
                        $resumen[1]['debe'] = number_format($resumen[1]['debe'],2,',','.');
                        $resumen[1]['haber'] = number_format($resumen[1]['haber'],2,',','.');
                        $muestra="yes";
                    }
                }
                
            } catch (\Exception $err) {
                //throw $th;
                $error = $err->getMessage() .' Line Erro:'.$err->getLine().' Program:';
            }
            //throw $this->createNotFoundException("Datos de ".$error);

        }

        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        return $this->render('views/consulta/query_gastos_af.html.twig', [
            'arrMenu'=>$arrMenu, 'muestra'=> $muestra, 'resumen'=>$resumen,'gastosAf'=>$gastosAf, 'error'=>$error,
            'fecha_desde'=>$fecha_desde, 'fecha_hasta'=>$fecha_hasta, 'mayorAux'=>$this->mayorAux, 'simejora'=>$simejora,
            'totalDebe'=>number_format($totalDebe,2,',','.'),'totalHaber'=>number_format($totalHaber,2,',','.'),
        ]);
    } 
    private $mayorAux=[];
    public function cuentaMayor(string $codcta, string $cuenta, float $monto,string $tipoCargo):bool
    {
        $valorDev = false;
        $items = count($this->mayorAux);

        for ($x = 0; $x<$items;$x++)
        {
            if ($this->mayorAux[$x]['codcta']==$codcta)
            {
                $valorDev = true;
                if ($tipoCargo=='D'){
                    $this->mayorAux[$x]['debe'] += $monto;
                    $this->mayorAux[$x] ['debeStr'] = number_format($this->mayorAux[$x]['debe'],2,',','.');
                } else {
                    $this->mayorAux[$x]['haber'] += $monto;
                    $this->mayorAux[$x] ['haberStr'] = number_format($this->mayorAux[$x]['haber'],2,',','.');
                }
            }
        };
        if ($valorDev==false){
            // No existe la cuenta
            $this->mayorAux[$items] = [
                'codcta'    => $codcta,
                'cuenta'    => $cuenta,
                'debe'      => 0.00,
                'haber'     => 0.00,
                'debeStr'   => 0.00,
                'haberStr'  => 0.00,
            ];
            if ($tipoCargo=='D'){
                $this->mayorAux[$items]['debe'] += $monto;
                $this->mayorAux[$items] ['debeStr'] = number_format($this->mayorAux[$x]['debe'],2,',','.');
            } else {
                $this->mayorAux[$items]['haber'] += $monto;
                $this->mayorAux[$items] ['haberStr'] = number_format($this->mayorAux[$x]['haber'],2,',','.');
            }
        }
        return $valorDev;
    }

    /**
     * @Route("/consulta/app/resumen/contab/list", name="app_resumen_contab")
     */
    public function listadoResumenContab(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $fechaDesde =new \DateTime($request->get('fechaDesde'));
            $fechaHasta =new \DateTime($request->get('fechaHasta'));
            $idPropiedad = $request->get('idPropiedad');
            $fecha_desde = $request->get('fechaDesde');
            $fecha_hasta = $request->get('fechaHasta');

        } else {

            $fecha_desde = "";
            $fecha_hasta = "";
        }        
        $idUsuario = $this->getUser()->getUserIdentifier();
        $arrMenu = $this->doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);

        $error="";
        $totalDebe = 0.00;
        $totalHaber = 0.00;
        $muestra="not";
        $simejora = "not";
        if (isset($fechaDesde))
        {
            try {
                //code...
                $em = $this->doctrine->getManager();
    
                $amortizs = $em->getRepository(Amortizaciones::class)->findByAmortizRango($fechaDesde,$fechaHasta,$idPropiedad);

                if ($amortizs['estado']=='yes'){
                    $idAfAnterior       = $amortizs['calculos'][0]['id_af'];
    
                    $token = 1;
                    $fechaValid = null;
                    $ipc_cierre = 0.00;
                    $ipc_origen = 0.00;
                    $factor_correc = 0.00;
                    $costo_ajust =0.00;
                    $ajust_act = 0.00;
                    $ajust_neto = 0.00;
                    $totalCapital=0.00;
                    $totalRAR =0.00;

                    foreach($amortizs['calculos'] as $fila){
        
                        if ($idAfAnterior!=$fila['id_af']){
                            // Carga la informacion 
                            $token = 1;
                        }
                        if ($token==1){
                            $fechaValid = (($fila['fecha']>$fechaDesde) ? $fila['fecha'] : $fechaDesde);
                            $ipc_cierre = $em->getRepository(IndicePrecio::class)->findOneBy(['mes'=>$fechaHasta->format('m'),'anio'=>$fechaHasta->format('Y')]);
                            $ipc_origen = $em->getRepository(IndicePrecio::class)->findOneBy(['mes'=>$fechaValid->format('m'),'anio'=>$fechaValid->format('Y')]);

                            $factor_correc = ($ipc_cierre->getFactor()/$ipc_origen->getFactor());
                            $costo_ajust = ($fila['costo_hist']*$factor_correc);
                            $ajust_act = $costo_ajust - $fila['costo_hist'];
                            $ajust_neto = $ajust_act;
        
                            $token = 0;
                            $idAfAnterior=$fila['id_af'];
                            $totalCapital+=$ajust_neto;
                            $totalRAR+=($ajust_neto*0.03);

                            $this->cuentaMayor($fila['id_clase'].'act',strtoupper($fila['clasific']),$ajust_act,"D");
                            $this->cuentaMayor($fila['id_clase'].'gto',"GASTOS DE DEPRECIACIÓN ".strtoupper($fila['clasific']),$fila['amortiz_ajust'],"D");
                            $this->cuentaMayor("CLA99902","TASA DE R.A.R. POR AMORTIZAR",($ajust_neto*0.03),"D");
                            $this->cuentaMayor($fila['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($fila['clasific']),$fila['amortiz_ajust'],"H");
                            $this->cuentaMayor("CLA99901","ACTUALIZACIÓN DE PATRIMONIO ".$fechaHasta->format("d/m/Y"),$ajust_neto,"H");
                            $this->cuentaMayor("CLA99903","TASA R.A.R. POR PAGAR (PASIVO CIRCULANTE-MONETARIO)",($ajust_neto*0.03),"H");

                        } // if token 
                    } // endfor
                }
                // Buscar contabilidad de Cuando Finiquito.
                $finiquitos = $em->getRepository(Finiquito::class)->findBy(["tipo_finiquito"=>"Mejora"]);
                
                foreach($finiquitos As $fila){
                    if ($fila->getFechaFiniquito()>=$fechaDesde && $fila->getFechaFiniquito()<=$fechaHasta){
                        $activo = $em->getRepository(Activofijo::class)->findOneByDataActivo($fila->getIdAf());
                        if ($fila->getCostoMej()>0){
                            // Finiquito por concepto de mejora.
                            // Finaliza la amoritacion del anterior activo fijo.
                            $this->cuentaMayor($activo['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($activo['clasific']),$fila->getDepAcumulada(),"D");
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($activo['clasific']),$fila->getDepAcumulada(),"H");
    
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($activo['clasific']),$fila->getCostoMej(),"D");
                            $this->cuentaMayor("CLA999908","RETENCION IMPUESTO FISCAL",($fila->getImpMej()+$fila->getImpFlete()),"D");
                            $this->cuentaMayor("CLA999909","GASTOS EN FLETE O TRASLADO",($fila->getCostoFlete()),"D");
                            $this->cuentaMayor('CLA999904',"CAJA - BANCO",($fila->getTotalMej()+$fila->getTotalFlete()),"H");
                        }
    
                    }
                }
                // Buscar contabilidad de Cuando Finiquito.
                $finiquitos = $em->getRepository(Finiquito::class)->findBy(["tipo_finiquito"=>"Venta"]);
                
                foreach($finiquitos As $fila){
                    if ($fila->getFechaFiniquito()>=$fechaDesde && $fila->getFechaFiniquito()<=$fechaHasta){
                        $activo = $em->getRepository(Activofijo::class)->findOneByDataActivo($fila->getIdAf());
                        if ($fila->getGananciaVta()>0){
                            // Finiquito por Venta Ganancia
                            $this->cuentaMayor($activo['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($activo['clasific']),$fila->getDepAcumulada(),"D");
                            $this->cuentaMayor('CLA999904',"CAJA - BANCO",$fila->getMontoVenta(),"D");
                            $this->cuentaMayor('CLA999905',"GANANCIA EN VENTA DE ACTIVO FIJO",$fila->getGananciaVta(),"H");
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($fila['clasific']),($fila->getDepAcumulada()+$fila->getCostoAjustado()),"H");
                        } elseif($fila->getPerdidaVta()>0){
                            // Finiquito por Venta Ganancia
                            $this->cuentaMayor($activo['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($activo['clasific']),$fila->getDepAcumulada(),"D");
                            $this->cuentaMayor('CLA999906',"PÉRDIDA EN VENTA DE ACTIVO FIJO",$fila->getGananciaVta(),"D");
                            $this->cuentaMayor('CLA999904',"CAJA - BANCO",$fila->getMontoVenta(),"D");
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($fila['clasific']),($fila->getDepAcumulada()+$fila->getCostoAjustado()),"H");
                        } elseif ($fila->getMontoVenta()==$fila->getCostoAjustado() || $fila->getTipoFiniquito()=="Obsoleto"){
                            // Finiquito sin Ganancia o Perdida; por obsoleto
                            $this->cuentaMayor($activo['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($activo['clasific']),$fila->getDepAcumulada(),"D");
                            if ($fila->getMontoVenta()>0){
                                $this->cuentaMayor('CLA999904',"CAJA - BANCO",$fila->getCostoAjustado(),"D");
                            } else {
                                $this->cuentaMayor('CLA999907',"PÉRDIDA EN ACTIVO FIJO",$fila->getCostoAjustado(),"D");
                            }
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($activo['clasific']),($fila->getDepAcumulada()+$fila->getCostoAjustado()),"H");
                        }
                    }
                }
                // Buscar contabilidad de Cuando Finiquito.
                $finiquitos = $em->getRepository(Finiquito::class)->findBy(["tipo_finiquito"=>"Obsoleto"]);
                
                foreach($finiquitos As $fila){
                    if ($fila->getFechaFiniquito()>=$fechaDesde && $fila->getFechaFiniquito()<=$fechaHasta){
                        $activo = $em->getRepository(Activofijo::class)->findOneByDataActivo($fila->getIdAf());
                        if ($fila->getMontoVenta()==$fila->getCostoAjustado() || $fila->getTipoFiniquito()=="Obsoleto"){
                            // Finiquito sin Ganancia o Perdida; por obsoleto
                            $this->cuentaMayor($activo['id_clase'].'dpr',"DEPRECIACIÓN ACUMULADA ".strtoupper($activo['clasific']),$fila->getDepAcumulada(),"D");
                            if ($fila->getMontoVenta()>0){
                                $this->cuentaMayor('CLA999904',"CAJA - BANCO",$fila->getCostoAjustado(),"D");
                            } else {
                                $this->cuentaMayor('CLA999907',"PÉRDIDA EN ACTIVO FIJO",$fila->getCostoAjustado(),"D");
                            }
                            $this->cuentaMayor($activo['id_clase'].'act',strtoupper($activo['clasific']),($fila->getDepAcumulada()+$fila->getCostoAjustado()),"H");
                        }
                    }
                }
                // registro contable por gastos
                $gastosAf = $em->getRepository(Mantenimiento::class)->findByGastosRango($fechaDesde,$fechaHasta,$idPropiedad);
                if (isset($gastosAf['message'])){
                    throw $this->createNotFoundException("Datos de ".$gastosAf['message']);
                }

                if ($gastosAf['estado']=='yes'){
                    $idClaseAnterior    = "";
                    $ajust_act = 0.00;
                    $ajust_depr = 0.00;
                    $ajust_neto = 0.00;
                    $poscRsm = 0;
        
                    foreach($gastosAf['calculos'] as $fila){
                        // Registro en el Mayor Aux
                        if ($fila['tipo_mant']=='Reparación')
                        {
                            if ($fila['traslado']['si']==1){
                                $this->cuentaMayor("CLA999908","GASTOS EN FLETE O TRASLADO",$fila['traslado']['costo_tras'],"D");
                                $this->cuentaMayor("CLA999909","RETENCIÓN IMPUESTO FISCAL",$fila['traslado']['imp_tras'],"D");
                                $this->cuentaMayor("CLA999904","CAJA - BANCO",$fila['traslado']['total_tras'],"H");
                            }
                            $this->cuentaMayor("CLA999910","GASTOS EN REPARACIONES",$fila['costo_fact'],"D");
                            $this->cuentaMayor("CLA999909","RETENCIÓN IMPUESTO FISCAL",$fila['imp_fact'],"D");
                            $this->cuentaMayor("CLA999904","CAJA - BANCO",$fila['total_fact'],"H");

                        } elseif($fila['tipo_mant']=='Mejora'){
                            $simejora = "yes";
                        }

                    } // endfor
                }
                // Totalizar el Debe y Haber 
                asort($this->mayorAux);
                $nroElements = count($this->mayorAux);
                for ($x=0;$x<$nroElements;$x++)
                {
                    $totalDebe += $this->mayorAux[$x]['debe'];
                    $totalHaber += $this->mayorAux[$x]['haber'];
                }
                $muestra = 'yes';
         
            } catch (\Exception $err) {
                //throw $th;
                $error = $err->getMessage() .' Line Erro:'.$err->getLine().' Program:';
            }
            //throw $this->createNotFoundException("Datos de ".$error);

        }

        return $this->render('views/consulta/query_resumen_contab.html.twig', [
            'arrMenu'=>$arrMenu,'error'=>$error,
            'fecha_desde'=>$fecha_desde, 'fecha_hasta'=>$fecha_hasta, 'mayorAux'=>$this->mayorAux,
            'totalDebe'=>number_format($totalDebe,2,',','.'),'totalHaber'=>number_format($totalHaber,2,',','.'),
            'muestra'=>$muestra,'simejora'=>$simejora,
        ]);

    }    

}
