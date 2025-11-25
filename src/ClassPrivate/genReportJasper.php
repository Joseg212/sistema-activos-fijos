<?php

namespace App\ClassPrivate;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;
use JasperPHP\JasperPHP; 

class genReportJasper {

    private $report;
    private $reportDest;
    private $param = array();

    public function __construct(string $jrxml,string $reportDest,array $param)
    {
        $this->report = $jrxml;
        $this->reportDest = $reportDest;
        $this->param = $param;
    }
    public  function runReport():string
    {
        
                
        // Crear el objeto JasperPHP
        $jasper = new JasperPHP("E:\\xampp\\htdocs\\appActivoFijo\\src\\ClassPrivate\\fonts");
        
        // Generar el Reporte
        $jasper->process($this->report,$this->reportDest,array('pdf', 'rtf'),$this->param,array('driver' => 'mysql','username' => 'root','host' => '127.0.0.1','database' => 'controlaf','port' => '3306'))->execute();

        return "procesado";        
    }

}