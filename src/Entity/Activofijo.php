<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\ActivofijoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActivofijoRepository::class)
 */
class Activofijo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $id_clase;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_ubic;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $code_activof;

    /**
     * @ORM\Column(type="string", length=160)
     * @Assert\NotBlank(message="*")
     */
    private $descrip;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="*")
     */
    private $fecha_compra;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="*")
     */
    private $distribuidor;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank(message="*")
     */
    private $rif;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="*")
     */
    private $nrofact;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     * @Assert\NotBlank(message="*")
     */
    private $costo;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     * @Assert\NotBlank(message="*")
     */
    private $impuesto;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     * @Assert\NotBlank(message="*")
     */
    private $costo_total;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     * @Assert\NotBlank(message="*")
     */
    private $costo_flete;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $edo_fisico;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $num_serie;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $estatus;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $marca;    

    public function getIdAf(): ?string
    {
        return $this->id_af;
    }

    public function setIdAf(?string $id_af): self
    {
        $this->id_af = $id_af;

        return $this;
    }

    public function getIdClase(): ?string
    {
        return $this->id_clase;
    }

    public function setIdClase(?string $id_clase): self
    {
        $this->id_clase = $id_clase;

        return $this;
    }

    public function getIdUbic(): ?string
    {
        return $this->id_ubic;
    }

    public function setIdUbic(string $id_ubic): self
    {
        $this->id_ubic = $id_ubic;

        return $this;
    }

    public function getCodeActivof(): ?string
    {
        return $this->code_activof;
    }

    public function setCodeActivof(?string $code_activof): self
    {
        $this->code_activof = $code_activof;

        return $this;
    }

    public function getDescrip(): ?string
    {
        return $this->descrip;
    }

    public function setDescrip(string $descrip): self
    {
        $this->descrip = $descrip;

        return $this;
    }

    public function getRif(): ?string
    {
        return $this->rif;
    }

    public function setRif(string $rif): self
    {
        $this->rif = $rif;

        return $this;
    }
    public function getFechaCompra(): ?\DateTimeInterface
    {
        return $this->fecha_compra;
    }

    public function setFechaCompra(?\DateTimeInterface $fecha_compra): self
    {
        $this->fecha_compra = $fecha_compra;

        return $this;
    }

    public function getDistribuidor(): ?string
    {
        return $this->distribuidor;
    }

    public function setDistribuidor(string $distribuidor): self
    {
        $this->distribuidor = $distribuidor;

        return $this;
    }

    public function getNrofact(): ?string
    {
        return $this->nrofact;
    }

    public function setNrofact(string $nrofact): self
    {
        $this->nrofact = $nrofact;

        return $this;
    }

    public function getCosto(): ?string
    {
        return $this->costo;
    }

    public function setCosto(?string $costo): self
    {
        $this->costo = $costo;

        return $this;
    }

    public function getImpuesto(): ?string
    {
        return $this->impuesto;
    }

    public function setImpuesto(?string $impuesto): self
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    public function getCostoTotal(): ?string
    {
        return $this->costo_total;
    }

    public function setCostoTotal(?string $costo_total): self
    {
        $this->costo_total = $costo_total;

        return $this;
    }

    public function getCostoFlete(): ?string
    {
        return $this->costo_flete;
    }

    public function setCostoFlete(?string $costo_flete): self
    {
        $this->costo_flete = $costo_flete;

        return $this;
    }

    public function getEdoFisico(): ?string
    {
        return $this->edo_fisico;
    }

    public function setEdoFisico(string $edo_fisico): self
    {
        $this->edo_fisico = $edo_fisico;

        return $this;
    }

    public function getNumSerie(): ?string
    {
        return $this->num_serie;
    }

    public function setNumSerie(?string $num_serie): self
    {
        $this->num_serie = $num_serie;

        return $this;
    }

    public function getEstatus(): ?string
    {
        return $this->estatus;
    }

    public function setEstatus(?string $estatus): self
    {
        $this->estatus = $estatus;

        return $this;
    }

    public function setMarca(?bool $marca): self
    {
        $this->marca = $marca;
        return $this;
    }
    public function getMarca(): ?bool
    {
        return $this->marca;
    }

    public function isMarca(): ?bool
    {
        return $this->marca;
    }
}
