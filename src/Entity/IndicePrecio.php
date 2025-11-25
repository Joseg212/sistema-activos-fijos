<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\IndicePrecioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=IndicePrecioRepository::class)
 */
class IndicePrecio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_ipc;

    /**
     * @ORM\Column(type="string", length=4)
     * @Assert\NotBlank(message="No Valido")
     */
    private $anio;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank(message="No Valido")
     */
    private $mes;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=6)
     * @Assert\NotBlank(message="No Valido")
     */
    private $factor;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $reconver;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="No Valido")
     */
    private $grupo;

    public function getIdIpc(): ?string
    {
        return $this->id_ipc;
    }

    public function setIdIpc(string $id_ipc): self
    {
        $this->id_ipc = $id_ipc;
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

    public function getMes(): ?string
    {
        return $this->mes;
    }

    public function setMes(string $mes): self
    {
        $this->mes = $mes;

        return $this;
    }

    public function getFactor(): ?string
    {
        return $this->factor;
    }

    public function setFactor(string $factor): self
    {
        $this->factor = $factor;

        return $this;
    }

    public function getReconver(): ?string
    {
        return $this->reconver;
    }

    public function setReconver(string $reconver): self
    {
        $this->reconver = $reconver;

        return $this;
    }

    public function getGrupo(): ?int
    {
        return $this->grupo;
    }

    public function setGrupo(int $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }
}
