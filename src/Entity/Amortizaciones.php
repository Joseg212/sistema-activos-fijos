<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\AmortizacionesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AmortizacionesRepository::class)
 */
class Amortizaciones
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_amortiz;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $mes;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anio;

    /**
     * @ORM\Column(type="integer")
     */
    private $periodo;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $residual;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=6)
     */
    private $factor_correc;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $revalorizado;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $amortiz_calc;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $amortiz_acum;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $costo_activo;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $costo_hist;


    /**
     * @ORM\Column(type="decimal", precision=30, scale=6)
     */
    private $factor_original;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $relacion_porc;

    /**
     * @ORM\Column(type="decimal", precision=30, scale=2)
     */
    private $reconversion;

    public function getIdAmortiz(): ?string
    {
        return $this->id;
    }

    public function setIdAmortiz(string $id_amortiz):self
    {
        $this->id_amortiz = $id_amortiz;

        return $this;
    }

    public function getIdAf(): ?string
    {
        return $this->id_af;
    }

    public function setIdAf(string $id_af): self
    {
        $this->id_af = $id_af;

        return $this;
    }

    public function getMes(): ?string
    {
        return $this->mes;
    }

    public function setMes(string $mes): self
    {
        $this->mes = $mes;

        return $this;
    }

    public function getAnio(): ?string
    {
        return $this->anio;
    }

    public function setAnio(string $anio): self
    {
        $this->anio = $anio;

        return $this;
    }

    public function getPeriodo(): ?int
    {
        return $this->periodo;
    }

    public function setPeriodo(int $periodo): self
    {
        $this->periodo = $periodo;

        return $this;
    }

    public function getResidual(): ?string
    {
        return $this->residual;
    }

    public function setResidual(string $residual): self
    {
        $this->residual = $residual;

        return $this;
    }

    public function getFactorCorrec(): ?string
    {
        return $this->factor_correc;
    }

    public function setFactorCorrec(string $factor_correc): self
    {
        $this->factor_correc = $factor_correc;

        return $this;
    }

    public function getRevalorizado(): ?string
    {
        return $this->revalorizado;
    }

    public function setRevalorizado(string $revalorizado): self
    {
        $this->revalorizado = $revalorizado;

        return $this;
    }

    public function getAmortizCalc(): ?string
    {
        return $this->amortiz_calc;
    }

    public function setAmortizCalc(string $amortiz_calc): self
    {
        $this->amortiz_calc = $amortiz_calc;

        return $this;
    }

    public function getAmortizAcum(): ?string
    {
        return $this->amortiz_acum;
    }

    public function setAmortizAcum(string $amortiz_acum): self
    {
        $this->amortiz_acum = $amortiz_acum;

        return $this;
    }

    public function getCostoActivo(): ?string
    {
        return $this->costo_activo;
    }

    public function setCostoActivo(string $costo_activo): self
    {
        $this->costo_activo = $costo_activo;

        return $this;
    }

    public function getCostoHist(): ?string
    {
        return $this->costo_hist;
    }

    public function setCostoHist(string $costo_hist):self
    {
        $this->costo_hist = $costo_hist;
        return $this;
    }
    
    public function getFactorOriginal():?string
    {
        return $this->factor_original;
    }
    
    public function setFactorOriginal(string $factor_original): self
    {
        $this->factor_original = $factor_original;
        return $this;
    }

    public function getRelacionPorc():?string
    {
        return $this->relacion_porc;
    }
    
    public function setRelacionPorc(string $relacion_porc):self
    {
        $this->relacion_porc = $relacion_porc;
        return $this;
    }

    public function getReconversion(): ?string
    {
        return $this->reconversion;
    }

    public function setReconversion(string $reconversion):self
    {
        $this->reconversion = $reconversion;
        return $this;
    }
}
