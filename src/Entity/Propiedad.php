<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\PropiedadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PropiedadRepository::class)
 */
class Propiedad
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $id_propiedad;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Assert\NotBlank(message="No Valido")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $encargado;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\NotBlank(message="No Valido")
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\NotBlank(message="No Valido")
     */
    private $nota;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\NotBlank(message="No Valido")
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $movil;

    public function getIdPropiedad(): ?string
    {
        return $this->id_propiedad;
    }

    public function setIdPropiedad(string $id_propiedad): self
    {
        $this->id_propiedad = $id_propiedad;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEncargado(): ?string
    {
        return $this->encargado;
    }

    public function setEncargado(?string $encargado): self
    {
        $this->encargado = $encargado;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getNota(): ?string
    {
        return $this->nota;
    }

    public function setNota(?string $nota): self
    {
        $this->nota = $nota;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getMovil(): ?string
    {
        return $this->movil;
    }

    public function setMovil(?string $movil): self
    {
        $this->movil = $movil;

        return $this;
    }
}
