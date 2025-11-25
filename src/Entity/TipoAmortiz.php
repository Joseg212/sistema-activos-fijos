<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\TipoAmortizRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TipoAmortizRepository::class)
 */
class TipoAmortiz
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_tipom;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $formula;

    /**
     * @ORM\Column(type="integer")
     */
    private $tiempo_estimado;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    private $valor_salvamento;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $observ;

    public function getIdTipom(): ?string
    {
        return $this->id_tipom;
    }

    public function setIdTipom(string $id_tipom):self
    {

        $this->id_tipom = $id_tipom;

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

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function getTiempoEstimado(): ?int
    {
        return $this->tiempo_estimado;
    }

    public function setTiempoEstimado(int $tiempo_estimado): self
    {
        $this->tiempo_estimado = $tiempo_estimado;

        return $this;
    }

    public function getValorSalvamento(): ?string
    {
        return $this->valor_salvamento;
    }

    public function setValorSalvamento(string $valor_salvamento): self
    {
        $this->valor_salvamento = $valor_salvamento;

        return $this;
    }

    public function getObserv(): ?string
    {
        return $this->observ;
    }

    public function setObserv(string $observ): self
    {
        $this->observ = $observ;

        return $this;
    }
}
