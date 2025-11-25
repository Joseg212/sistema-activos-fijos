<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\PermisoMenuRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PermisoMenuRepository::class)
 */
class PermisoMenu
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_permiso;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $permiso;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $id_opcion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_us;

    public function getIdPermiso(): ?string
    {
        return $this->id_permiso;
    }

    public function setIdPermiso(?string $id_permiso): self
    {
        $this->id_permiso = $id_permiso;

        return $this;
    }


    public function getPermiso(): ?string
    {
        return $this->permiso;
    }

    public function setPermiso(?string $permiso): self
    {
        $this->permiso = $permiso;

        return $this;
    }

    public function getIdOpcion(): ?string
    {
        return $this->id_opcion;
    }

    public function setIdOpcion(?string $id_opcion): self
    {
        $this->id_opcion = $id_opcion;

        return $this;
    }

    public function getIdUs(): ?int
    {
        return $this->id_us;
    }

    public function setIdUs(?int $id_us): self
    {
        $this->id_us = $id_us;

        return $this;
    }
}
