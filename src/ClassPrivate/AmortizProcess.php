<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Activofijo;
use App\Entity\Clasificacion;
use App\Entity\Propiedad;
use App\Entity\Ubicacion;
use App\Entity\Amortizaciones;
use App\Entity\IndicePrecio;
use App\Entity\TipoAmortiz;
use Doctrine\Persistence\ManagerRegistry;



class AmortizProcess
{
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
       $this->doctrine = $doctrine;
    }

    /* Variable de Parametros iniciales */
    private  $id_ActivoFijo = "";
    private  $id_Propiedad = "";
    private  $id_Ubicacion = "";
    private  $FechaActual;  

    /* Variable de Proceso */
    private $ErrorMensaje = "";

    public function setIdPropiedad(string $id_prop):self
    {
        $this->id_Propiedad = $id_prop;       
        return $this;
    }
    public function setIdUbicacion(string $id_ubic):self
    {
        $this->id_Ubicacion = $id_ubic;
        return $this;
    }

    public function setIdActivoFijo(string $id_af):self
    {
        $this->id_ActivoFijo = $id_af;
        return $this;
    }   

    public function setFechaActual(\DateTimeInterface $fechaActual):self
    {
        $this->FechaActual = $fechaActual;
        return $this;
    }
    public function getErrorMensaje():string
    {
        return $this->ErrorMensaje;
    }
    /* Construir la tabla  de amortizacion en la tabla amortizacion */
    public function generarAmortizaciones():bool
    {
        $procesoTerminado = false;

        $em = $this->doctrine->getManager();

        if ($this->id_Ubicacion==""){
            $this->ErrorMensaje="Debe definir el id ubicación!!";
            return false;
        }
        if (!isset($this->FechaActual) && empty($this->FechaActual)){
            $this->ErrorMensaje="Debe indicar la Fecha Actual!!";
            return false;
        }
        // Inicia el proceso de los activos fijos.
        try{
            $rep_af = $this->doctrine->getRepository(Activofijo::class);
            $rep_tipom = $this->doctrine->getRepository(TipoAmortiz::class);
    
            $activos = $rep_af->findBy(['estatus'=>'activo']);
    
            // Variable del Proceso 
            $ciclo=0;
            $periodo=1;
            $fechaActivo = null;
            $mes = "";
            $anio ="";
            $contarMeses=1;
            $residual=0;

            $mesInicial="";
            $anioInicial="";

            $amortizacionDelMes=0;
            $acumAmortizacion=0;
            $costoActivo = 0;

            $revalorizado = 0.00;
            $factorC = array();
            $reconverMoneda = 0.00;
            $factorOriginal = 0.00;
            $relacionPorc = 0.00;
            
            $amortizNew = null;
            $contadorMeses  =0;

            foreach($activos As $activo){
                // Ubicar el metodo de amortizacion del activo

                $verifcarAmortiz = $em->getRepository(Amortizaciones::class)->findByVerificarAmortiz($activo->getIdAf());

                if ($verifcarAmortiz!=null && $verifcarAmortiz['total']==$verifcarAmortiz['tiempo_est'])
                {
                    continue;
                } 

                $tipoAmortiz = $rep_tipom->findOneBy(['id_af'=>$activo->getIdAf()]);
                if ($tipoAmortiz){
                    $residual = abs($activo->getCosto()-$tipoAmortiz->getValorSalvamento());
                } else {
                    continue;
                }


                $revalorizado = 0.00;
                $factorC = array();
                $reconverMoneda = 0.00;

                $ciclo =  $tipoAmortiz->getTiempoEstimado();

                $fechaActivo = $activo->getFechaCompra();
                
                $anioReg = $fechaActivo->format("Y");

                $contarMeses=1;
                $periodo=1;
                $acumAmortizacion=0;
                $costoActivo = $activo->getCosto();
                $factorC = array();
                $reconverMoneda = 0.00;
                $amortizacionDelMes=0.00;
                $contadorMeses  =0;
                $factorOriginal =0.00;
                $relacionPorc = 0.00;


                // Para los Fines Inflacionario 
                $mesInicial = $fechaActivo->format("m");
                $anioInicial = $fechaActivo->format("Y");

                if (($verifcarAmortiz!=null && $verifcarAmortiz['total']>0)) {
                    $contadorMeses = $verifcarAmortiz['total']-1;
                    $fechaActivo->add(new \DateInterval("P{$contadorMeses}M"));
                    $decimal = floor($contadorMeses/12);
                    $contarMeses=round(($decimal*12),0);
                    $periodo = round(($contadorMeses/12),0);
                    $acumAmortizacion = 100;
                    if ($decimal !=0 && $decimal <0.5)
                    {
                        $periodo++;
                    }
                    // Si hay reconversion monetario en el lapso de tiempo.
                    $desde = $anioInicial.$mesInicial;
                    $hasta = $fechaActivo->Format("Ym");
                    $reconverMoneda = $em->getRepository(IndicePrecio::class)->findByReconver($desde, $hasta);
                }            
                while ($ciclo!= 0){
                    $contadorMeses++;

                    $mes = $fechaActivo->format("m");
                    $anio = $fechaActivo->format("Y");

                    $factorC = $em->getRepository(IndicePrecio::class)->findByFactorDeCorrecion($mesInicial,$anioInicial,$mes,$anio);
                    $factorOriginal = $factorC['factor'];
                    // Si el factor es Nulo no continuar el proceso
                    if ($factorC['factor']==0){
                        $ciclo=0;
                        continue;
                    }
                    // Verificar si hay reconversion monetaria
                    $revalorizado = $residual;
                    $costoActivo = $activo->getCosto();

                    if ($factorC['reconver']>0){
                        if ($reconverMoneda==0){
                            $reconverMoneda = $factorC['reconver'];
                        } else {
                            $reconverMoneda = $factorC['reconver']*$reconverMoneda;
                        }
                        //$acumAmortizacion = round(($acumAmortizacion/$factorC["reconver"]),2);
                    } 

                    if ($revalorizado==0 && $mes==$mesInicial && $anio==$anioInicial){
                        $revalorizado = round(($factorC['factor']*$residual),2);
                    } else {
                        if ($revalorizado>0 && !($mes==$mesInicial && $anio==$anioInicial)){
                            $factorC['factor'] = $this->aplicarReconversion($factorC['factor'],$reconverMoneda,$fechaActivo);

                            $revalorizado = round(($factorC['factor']*$residual),2);
                            $costoActivo = round((($factorC['factor']*$activo->getCosto())),2);
                            //$costoActivo = $this->aplicarReconversion($costoActivo,$reconverMoneda,$fechaActivo);
                        }
                    }


                    $calculos = $this->formulaDepreciacion($tipoAmortiz->getFormula(),$revalorizado,
                                    $tipoAmortiz->getTiempoEstimado(),
                                    $periodo,$contadorMeses);

                    $amortizacionDelMes = $calculos['calculoDelMes'];
                    $relacionPorc = $calculos['relacionPorc'];

                    $acumAmortizacion+=$amortizacionDelMes;

                    if($acumAmortizacion==$amortizacionDelMes){
                        //Primer registro de la amortizacion 
                        $amortizNew = new Amortizaciones();

                        $amortizNew->setIdAmortiz("");
                        $amortizNew->setIdAf($activo->getIdAf());
                        $amortizNew->setMes($mes);
                        $amortizNew->setAnio($anio);
                        $amortizNew->setPeriodo(0);
                        $amortizNew->setCostoHist(0.00);
                        $amortizNew->setResidual(0.00);
                        $amortizNew->setFactorCorrec(0.00);
                        $amortizNew->setRevalorizado(0.0);
                        $amortizNew->setAmortizCalc(0.00);
                        $amortizNew->setAmortizAcum(0.00);
                        $amortizNew->setCostoActivo($costoActivo);
                        $amortizNew->setFactorOriginal(0.00);
                        $amortizNew->setRelacionPorc(100.00);
                        $amortizNew->setReconversion(0.00);


                        $em->persist($amortizNew);
                        $em->flush();                        
                    }
                    $acumAmortizacion = $calculos['acumuladoNuevo'];
                    $amortizNew = new Amortizaciones();

                    
                    $amortizNew->setCostoHist($costoActivo);

                    $costoActivo=round(($costoActivo-$acumAmortizacion),0);

                    $amortizNew->setIdAmortiz("");
                    $amortizNew->setIdAf($activo->getIdAf());
                    $amortizNew->setMes($mes);
                    $amortizNew->setAnio($anio);
                    $amortizNew->setPeriodo($periodo);
                    $amortizNew->setResidual($residual);
                    $amortizNew->setFactorCorrec($factorC['factor']);
                    $amortizNew->setRevalorizado($revalorizado);
                    $amortizNew->setAmortizCalc($amortizacionDelMes);
                    $amortizNew->setAmortizAcum($acumAmortizacion);
                    $amortizNew->setCostoActivo($costoActivo);
                    $amortizNew->setReconversion($reconverMoneda);
                    $amortizNew->setFactorOriginal($factorOriginal);
                    $amortizNew->setRelacionPorc($relacionPorc);

                    $em->persist($amortizNew);
                    $em->flush(); 

                    if ($contarMeses==12){
                        $ciclo--;
                        $periodo++;
                        $contarMeses=0;
                    }
                    $contarMeses++;
                    // Se incrementa la fecha en un mes
                    $fechaActivo->add(new \DateInterval("P1M"));
                    if ($fechaActivo>$this->FechaActual){
                        $ciclo=0;
                    }
                }
            } // Fin  Foreach
            $procesoTerminado=true;

        } catch(\Exception $err){
            $this->ErrorMensaje="Error:".$err->getMessage()." ".$err->getLine();
        }
        return $procesoTerminado;
    } 
    /* Formula de Depreciación  Aplicar */  
    public function formulaDepreciacion(string $formula,float $residual, int $tiempo, int $periodo,int $total_meses):array
    {
        $valor = array();
        $valor['calculoDelMes']=0.00;
        $valor['relacionPorc']=0.00;
        $valorSumaDigitos=0;
        $montoRelacionP = 0.00;

        switch ($formula)
        {
            case 'LINEA RECTA':
                $valor['calculoDelMes'] = (double)(($residual/$tiempo)/12);

                $valor['acumuladoNuevo'] = $valor['calculoDelMes']*$total_meses;
                // Para relacion porcentual 
                $montoRelacionP = $valor['calculoDelMes']*($tiempo*12);
                $valor['relacionPorc'] = (100-round(($valor['acumuladoNuevo']/$montoRelacionP)*100,2));
                

                break;
            case 'MÉTODO DECRECIENTE':
                $valor['calculoDelMes'] = (double)(($residual*((2*($tiempo-$periodo+1))/((1+$tiempo)*$tiempo)))/12);

                $valor['acumuladoNuevo'] = 0;
                for ($x=1;$x<=$tiempo;$x++)
                {
                    $valor['MesTemporal'] = (double)(($residual*((2*($tiempo-$x+1))/((1+$tiempo)*$tiempo)))/12);
                    if ($x<=$periodo){
                        if ($total_meses>12){
                            $valor['acumuladoNuevo'] += $valor['MesTemporal']*12;
                            $total_meses-=12;
                        } else {
                            $valor['acumuladoNuevo'] += $valor['MesTemporal']*$total_meses;
                        }
                    }
                    $montoRelacionP += $valor['MesTemporal']*12;
                }
                $valor['relacionPorc'] =(100-round(($valor['acumuladoNuevo']/$montoRelacionP)*100,2));
                break;
            case 'MÉTODO CRECIENTE':
                for ($i=1;$i<=$tiempo;$i++){
                    $valorSumaDigitos+=$i;
                }
                $valor['calculoDelMes'] = (double)(($residual*($periodo/$valorSumaDigitos))/12);

                $valor['acumuladoNuevo'] = 0;
                for ($x=1;$x<=$tiempo;$x++)
                {
                    $valor['MesTemporal'] = (double)(($residual*($x/$valorSumaDigitos))/12);
                    if ($x<=$periodo){
                        if ($total_meses>12){
                            $valor['acumuladoNuevo'] += $valor['MesTemporal']*12;
                            $total_meses-=12;
                        } else {
                            $valor['acumuladoNuevo'] += $valor['MesTemporal']*$total_meses;
                        }
                    }
                    $montoRelacionP += $valor['MesTemporal']*12;
                }
                $valor['relacionPorc']= (100 - round(($valor['acumuladoNuevo']/$montoRelacionP)*100,2));
                break;
        }
        
        $valor['calculoDelMes'] = round($valor['calculoDelMes'],2);

        return $valor;
    }
    //  Aplica la reconversion monetaria si aplica el caso
    private function aplicarReconversion(float $montoActual,float $reduccion,\DateTime $fechaActivo):float
    {
        $valorReconvertido = $montoActual;
        switch (true){
            case ($fechaActivo >= \DateTime::createFromFormat('d/m/Y', "01/01/2008") && $reduccion>0):
                $valorReconvertido = round(($montoActual/$reduccion),6);
                break;
            case ($fechaActivo >= \DateTime::createFromFormat('d/m/Y', "01/01/2018") && $reduccion>0):
                $valorReconvertido = round(($montoActual/$reduccion),6);
                break;
            case ($fechaActivo >= \DateTime::createFromFormat('d/m/Y', "01/10/2021") && $reduccion>0):
                $valorReconvertido = round(($montoActual/$reduccion),6);
                break;
        }        
        return $valorReconvertido;
    } 
    // Subtraer datos para informacion del activo
    public function loadInfoAmortiz(\DateTimeInterface $fecha,string $id_activo):array
    {
        $infoAmortiz=[];

        $em = $this->doctrine->getManager();

        $ultimoReg  = $em->getRepository(Amortizaciones::class)->findByObtenerMaximoId($id_activo);
        
        if ($ultimoReg==''){
            $infoAmortiz['error']='20';
            $infoAmortiz['costo_actual']    = '0.00';
            $infoAmortiz['factor_inflac']   = '0.00';
            $infoAmortiz['costo_ajustado']  = '0.00';
            $infoAmortiz['dep_acumulada']   = '0.00';
        } else {
            $amortizacion = $em->getRepository(Amortizaciones::class)->find($ultimoReg);
        

            $mesInicial = $amortizacion->getMes();
            $anioInicial = $amortizacion->getAnio();
    
            $mesIngresado = $fecha->format("m");
            $anioIngresado = $fecha->format("Y");
    
            $infoAmortiz['costo_actual'] = number_format($amortizacion->getCostoActivo(),2,'.',',');
            $infoAmortiz['error']=0;
    
            // Si es el mismo mes y año no es necesario aplicar los calculos
            if ($mesIngresado==$mesInicial && $anioIngresado==$anioInicial){
                $infoAmortiz['factor_inflac'] = number_format(1.00,6,'.',',');
                $infoAmortiz['costo_ajustado'] = number_format($amortizacion->getCostoActivo(),2,'.',',');
                $infoAmortiz['dep_acumulada'] = number_format($amortizacion->getAmortizAcum(),2,'.',',');
                
    
            } else {
                // Se determina el factor de correcion 
                $desde = $anioInicial.$mesInicial;
                $hasta = $anioIngresado.$mesIngresado;
                $factorC = array();
                $reconverMoneda = $em->getRepository(IndicePrecio::class)->findByReconver($desde, $hasta);
        
                $factorC = $em->getRepository(IndicePrecio::class)->findByFactorDeCorrecion($mesInicial,$anioInicial,$mesIngresado,$anioIngresado);
                if ($reconverMoneda!=0){
                    $factorC['factor'] = round(($factorC['factor']/$reconverMoneda),6);
                }
                
                $infoAmortiz['factor_inflac'] = number_format($factorC['factor'],6,'.',',');
                $infoAmortiz['costo_ajustado'] = number_format(($amortizacion->getCostoActivo()*$factorC['factor']),2,'.',',');
                $infoAmortiz['dep_acumulada'] = number_format(($amortizacion->getAmortizAcum()*$factorC['factor']),2,'.',',');
                if ($factorC['factor']==1.000000){
                    // por que el factor decorrecion no puede ser uno
                   // $infoAmortiz['error'] = 30;
                }
        
            }
            // Falla si la amortizacion es superior a la fecha indicada.
            if ((integer)($anioInicial.$mesInicial)>(integer)($anioIngresado.$mesIngresado)){
                $infoAmortiz['error']='10';
            }
        }
        return $infoAmortiz;
    }
}