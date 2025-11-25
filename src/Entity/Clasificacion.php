<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\ClasificacionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ClasificacionRepository::class)
 */
class Clasificacion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $id_clase;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="No Valido")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="No Valido")
     */
    private $cod_cuenta;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $observacion;

    public function getIdClase(): ?string
    {
        return $this->id_clase;
    }

    public function setIdClase(string $id_clase): self
    {
        $this->id_clase = $id_clase;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCodCuenta(): ?string
    {
        return $this->cod_cuenta;
    }

    public function setCodCuenta(string $cod_cuenta): self
    {
        $this->cod_cuenta = $cod_cuenta;

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
