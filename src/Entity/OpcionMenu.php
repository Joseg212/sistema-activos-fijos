<?php

namespace App\Entity;

use App\Repository\OpcionMenuRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OpcionMenuRepository::class)
 */
class OpcionMenu
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $id_opcion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nivel;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $orden;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $opcion;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $grupo;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ruta;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $icono;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $estatus;

    public function getIdOpcion(): ?string
    {
        return $this->id;
    }

    public function setIdOpcion(?string $id_opcion): self
    {
        $this->id_opcion = $id_opcion;
        
        return $this;
    }

    public function getNivel(): ?int
    {
        return $this->nivel;
    }

    public function setNivel(?int $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }

    public function getOrden(): ?string
    {
        return $this->orden;
    }

    public function setOrden(?string $orden): self
    {
        $this->orden = $orden;

        return $this;
    }

    public function getOpcion(): ?string
    {
        return $this->opcion;
    }

    public function setOpcion(?string $opcion): self
    {
        $this->opcion = $opcion;

        return $this;
    }

    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function setGrupo(?string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(?string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getIcono(): ?string
    {
        return $this->icono;
    }

    public function setIcono(?string $icono): self
    {
        $this->icono = $icono;

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
}
