<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\TrasladoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrasladoRepository::class)
 */
class Traslado
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=25)
     */
    private $id_traslado;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="El id de activo fijo no puede ser blanco o nulo!!")
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $email_user;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Fecha no puede ser nula")
     *
     */
    private $fecha_traslado;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank(message="Se debe especificar el tipo de traslado!!")
     */
    private $tipo_traslado;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $tipo_des;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Id de Responsable emisor no puede ser nulo!!")
     */
    private $id_resp_emisor;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Id de Responsable destino no puede ser nulo!!")
     */
    private $id_resp_destino;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(message="Id de Ubicación origen no puede ser nulo!!")
     */
    private $id_ubic_orig;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\NotBlank(message="Id de Ubicación destino no puede ser nulo!!")
     */
    private $id_ubic_dest;

    /**
     * @ORM\Column(type="boolean")
     */
    private $destino_externo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $destino_externo_ubic;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $destino_externo_info;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fec_recep;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Debe escribir el motivo por el cual se mueve el activo a otro lugar.")
     */
    private $motivo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observ;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $estatus;

    public function getIdTraslado(): ?string
    {
        return $this->id_traslado;
    }

    public function setIdTraslado(string $id_traslado):self
    {
        $this->id_traslado = $id_traslado;
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

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser(string $email_user): self
    {
        $this->email_user = $email_user;

        return $this;
    }

    public function getFechaTraslado(): ?\DateTimeInterface
    {
        return $this->fecha_traslado;
    }

    public function setFechaTraslado(\DateTimeInterface $fecha_traslado): self
    {
        $this->fecha_traslado = $fecha_traslado;

        return $this;
    }

    public function getTipoTraslado(): ?string
    {
        return $this->tipo_traslado;
    }

    public function setTipoTraslado(string $tipo_traslado): self
    {
        $this->tipo_traslado = $tipo_traslado;

        return $this;
    }

    public function getTipoDes(): ?string
    {
        return $this->tipo_des;
    }

    public function setTipoDes(string $tipo_des): self
    {
        $this->tipo_des = $tipo_des;

        return $this;
    }

    public function getIdRespEmisor(): ?string
    {
        return $this->id_resp_emisor;
    }

    public function setIdRespEmisor(string $id_resp_emisor): self
    {
        $this->id_resp_emisor = $id_resp_emisor;

        return $this;
    }

    public function getIdRespDestino(): ?string
    {
        return $this->id_resp_destino;
    }

    public function setIdRespDestino(string $id_resp_destino): self
    {
        $this->id_resp_destino = $id_resp_destino;

        return $this;
    }

    public function getIdUbicOrig(): ?string
    {
        return $this->id_ubic_orig;
    }

    public function setIdUbicOrig(string $id_ubic_orig): self
    {
        $this->id_ubic_orig = $id_ubic_orig;

        return $this;
    }

    public function getIdUbicDest(): ?string
    {
        return $this->id_ubic_dest;
    }

    public function setIdUbicDest(?string $id_ubic_dest): self
    {
        $this->id_ubic_dest = $id_ubic_dest;

        return $this;
    }

    public function getDestinoExterno(): ?bool
    {
        return $this->destino_externo;
    }

    public function setDestinoExterno(bool $destino_externo): self
    {
        $this->destino_externo = $destino_externo;

        return $this;
    }

    public function getDestinoExternoUbic(): ?string
    {
        return $this->destino_externo_ubic;
    }

    public function setDestinoExternoUbic(string $destino_externo_ubic): self
    {
        $this->destino_externo_ubic = $destino_externo_ubic;

        return $this;
    }

    public function getDestinoExternoInfo(): ?string
    {
        return $this->destino_externo_info;
    }

    public function setDestinoExternoInfo(?string $destino_externo_info): self
    {
        $this->destino_externo_info = $destino_externo_info;

        return $this;
    }

    public function getFecRecep(): ?\DateTimeInterface
    {
        return $this->fec_recep;
    }

    public function setFecRecep(?\DateTimeInterface $fec_recep): self
    {
        $this->fec_recep = $fec_recep;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getObserv(): ?string
    {
        return $this->observ;
    }

    public function setObserv(?string $observ): self
    {
        $this->observ = $observ;

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

    public function isDestinoExterno(): ?bool
    {
        return $this->destino_externo;
    }
}
