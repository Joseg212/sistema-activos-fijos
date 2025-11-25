<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\UbicacionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UbicacionRepository::class)
 */
class Ubicacion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $id_ubic;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_propiedad;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="No Valido")
     */
    private $ubicacion;

    /**
     * @ORM\Column(type="string", length=160, nullable=true)
     */
    private $nota;

    public function getIdUbic(): ?string
    {
        return $this->id_ubic;
    }

    public function setIdUbic(string $id_ubic):self
    {
        $this->id_ubic = $id_ubic;
        
        return $this;
    }
    public function getIdPropiedad(): ?string
    {
        return $this->id_propiedad;
    }

    public function setIdPropiedad(string $id_propiedad): self
    {
        $this->id_propiedad = $id_propiedad;

        return $this;
    }

    public function getUbicacion(): ?string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(string $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

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
}
