<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\FiniquitoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FiniquitoRepository::class)
 */
class Finiquito
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $id_fin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="Ingrese la fecha de factura!!")
     */
    private $fecha_finiquito;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $tipo_finiquito;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_actual;

    /**
     * @ORM\Column(type="decimal", precision=30, scale=6)
     */
    private $factor_inflac;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_ajustado;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $monto_venta;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $dep_acumulada;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $ganancia_vta;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $perdida_vta;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_mej;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $imp_mej;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $total_mej;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_flete;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $imp_flete;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $total_flete;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $nueva_descrip;

    /**
     * @ORM\Column(type="text")
     */
    private $observacion;

    public function getIdFin(): ?string
    {
        return $this->id_fin;
    }

    public function setIdFin(string $id_fin):self
    {
        $this->id_fin = $id_fin;
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

    public function getTipoFiniquito(): ?string
    {
        return $this->tipo_finiquito;
    }

    public function setTipoFiniquito(string $tipo_finiquito): self
    {
        $this->tipo_finiquito = $tipo_finiquito;

        return $this;
    }

    public function getFechaFiniquito(): ?\DateTimeInterface
    {
        return $this->fecha_finiquito;
    }

    public function setFechaFiniquito(\DateTimeInterface $fecha_finiquito): self
    {
        $this->fecha_finiquito = $fecha_finiquito;

        return $this;
    }


    public function getCostoActual(): ?string
    {
        return $this->costo_actual;
    }

    public function setCostoActual(string $costo_actual): self
    {
        $this->costo_actual = $costo_actual;

        return $this;
    }

    public function getFactorInflac(): ?string
    {
        return $this->factor_inflac;
    }

    public function setFactorInflac(string $factor_inflac): self
    {
        $this->factor_inflac = $factor_inflac;

        return $this;
    }

    public function getCostoAjustado(): ?string
    {
        return $this->costo_ajustado;
    }

    public function setCostoAjustado(string $costo_ajustado): self
    {
        $this->costo_ajustado = $costo_ajustado;

        return $this;
    }

    public function getMontoVenta(): ?string
    {
        return $this->monto_venta;
    }

    public function setMontoVenta(string $monto_venta): self
    {
        $this->monto_venta = $monto_venta;

        return $this;
    }

    public function getDepAcumulada(): ?string
    {
        return $this->dep_acumulada;
    }

    public function setDepAcumulada(string $dep_acumulada): self
    {
        $this->dep_acumulada = $dep_acumulada;

        return $this;
    }

    public function getGananciaVta(): ?string
    {
        return $this->ganancia_vta;
    }

    public function setGananciaVta(string $ganancia_vta): self
    {
        $this->ganancia_vta = $ganancia_vta;

        return $this;
    }

    public function getPerdidaVta(): ?string
    {
        return $this->perdida_vta;
    }

    public function setPerdidaVta(string $perdida_vta): self
    {
        $this->perdida_vta = $perdida_vta;

        return $this;
    }

    public function getCostoMej(): ?string
    {
        return $this->costo_mej;
    }

    public function setCostoMej(string $costo_mej): self
    {
        $this->costo_mej = $costo_mej;

        return $this;
    }

    public function getImpMej(): ?string
    {
        return $this->imp_mej;
    }

    public function setImpMej(string $imp_mej): self
    {
        $this->imp_mej = $imp_mej;

        return $this;
    }

    public function getTotalMej(): ?string
    {
        return $this->total_mej;
    }

    public function setTotalMej(string $total_mej): self
    {
        $this->total_mej = $total_mej;

        return $this;
    }

    public function getCostoFlete(): ?string
    {
        return $this->costo_flete;
    }

    public function setCostoFlete(string $costo_flete): self
    {
        $this->costo_flete = $costo_flete;

        return $this;
    }

    public function getImpFlete(): ?string
    {
        return $this->imp_flete;
    }

    public function setImpFlete(string $imp_flete): self
    {
        $this->imp_flete = $imp_flete;

        return $this;
    }

    public function getTotalFlete(): ?string
    {
        return $this->total_flete;
    }

    public function setTotalFlete(string $total_flete): self
    {
        $this->total_flete = $total_flete;

        return $this;
    }

    public function getNuevaDescrip(): ?string
    {
        return $this->nueva_descrip;
    }

    public function setNuevaDescrip(string $nueva_descrip): self
    {
        $this->nueva_descrip = $nueva_descrip;

        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }
}
