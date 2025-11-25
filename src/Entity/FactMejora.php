<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\FactMejoraRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactMejoraRepository::class)
 */
class FactMejora
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=25)
     */
    private $id_det;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $id_mant;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $proveedor;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $proveedor_rif;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_fact;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nro_fact;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $telefono_prov;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_fact;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $imp_fact;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $total_fact;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $detalle;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $estatus;

    public function getIdDet(): ?string
    {
        return $this->id_det;
    }

    public function setIdDet(string $id_det):self
    {
        $this->id_det=$id_det;
        return $this;
    }

    public function getIdMant(): ?string
    {
        return $this->id_mant;
    }

    public function setIdMant(string $id_mant): self
    {
        $this->id_mant = $id_mant;

        return $this;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function setProveedor(string $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getProveedorRif(): ?string
    {
        return $this->proveedor_rif;
    }

    public function setProveedorRif(string $proveedor_rif): self
    {
        $this->proveedor_rif = $proveedor_rif;

        return $this;
    }

    public function getFechaFact(): ?\DateTimeInterface
    {
        return $this->fecha_fact;
    }

    public function setFechaFact(\DateTimeInterface $fecha_fact): self
    {
        $this->fecha_fact = $fecha_fact;

        return $this;
    }

    public function getNroFact(): ?string
    {
        return $this->nro_fact;
    }

    public function setNroFact(string $nro_fact): self
    {
        $this->nro_fact = $nro_fact;

        return $this;
    }

    public function getTelefonoProv(): ?string
    {
        return $this->telefono_prov;
    }

    public function setTelefonoProv(string $telefono_prov): self
    {
        $this->telefono_prov = $telefono_prov;

        return $this;
    }

    public function getCostoFact(): ?string
    {
        return $this->costo_fact;
    }

    public function setCostoFact(string $costo_fact): self
    {
        $this->costo_fact = $costo_fact;

        return $this;
    }

    public function getImpFact(): ?string
    {
        return $this->imp_fact;
    }

    public function setImpFact(string $imp_fact): self
    {
        $this->imp_fact = $imp_fact;

        return $this;
    }

    public function getTotalFact(): ?string
    {
        return $this->total_fact;
    }

    public function setTotalFact(string $total_fact): self
    {
        $this->total_fact = $total_fact;

        return $this;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(string $detalle): self
    {
        $this->detalle = $detalle;

        return $this;
    }

    public function getEstatus(): ?string
    {
        return $this->estatus;
    }

    public function setEstatus(string $estatus): self
    {
        $this->estatus = $estatus;

        return $this;
    }
}
