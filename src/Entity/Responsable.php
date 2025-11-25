<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\ResponsableRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ResponsableRepository::class)
 */
class Responsable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_resp;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="No Valido")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="No Valido")
     */
    private $apellido;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="No Valido")
     */
    private $cargo;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="No Valido")
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $movil;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_reg;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $estatus;

    public function getIdResp(): ?string
    {
        return $this->id_resp;
    }

    public function setIdResp(string $id_resp): self
    {
        $this->id_resp = $id_resp;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    public function setCargo(string $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getMovil(): ?string
    {
        return $this->movil;
    }

    public function setMovil(string $movil): self
    {
        $this->movil = $movil;

        return $this;
    }

    public function getFechaReg(): ?\DateTimeInterface
    {
        return $this->fecha_reg;
    }

    public function setFechaReg(\DateTimeInterface $fecha_reg): self
    {
        $this->fecha_reg = $fecha_reg;

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
