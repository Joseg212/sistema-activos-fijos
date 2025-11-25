<?php

namespace App\Entity;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Repository\MantenimientoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MantenimientoRepository::class)
 */
class Mantenimiento
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id_mant;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $id_af;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Indique el responsable que realizo la reparación!!")
     */
    private $id_resp;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $tipo_mant;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="Ingrese la fecha de factura!!")
     */
    private $fecha_fact;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Escriba el número de la factura!!")
     */
    private $nro_fact;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Indique el nombre o razón social del proveedor de servicio!!")
     */
    private $proveedor;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank(message="Indique el Rif del proveedor de servicio!!")
     */
    private $proveedor_rif;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $telefono_prov;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     * @Assert\NotBlank(message="Escriba el costo del servicio o factura!!")
     */
    private $monto_fact;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $monto_iva;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $total_factura;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $unidad_tiempo;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Indique el tiempo empleado para solucionar el problema en el campo numero!!")
     */
    private $numero_tiempo;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Debe indicar el detalle de la reparación!!")
     */
    private $detalle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $si_traslado;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $costo_traslado;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $imp_traslado;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $total_traslado;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="Ingrse el banco emisor del pago!!")
     */
    private $banco;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $tipo_doc;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Debe escribir el número de cocumento!!")
     */
    private $numero_doc;

    public function getIdMant(): ?string
    {
        return $this->id_mant;
    }

    public function setIdMant(string $id_mant):self
    {
        $this->id_mant = $id_mant;
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

    public function getIdResp(): ?string
    {
        return $this->id_resp;
    }

    public function setIdResp(string $id_resp): self
    {
        $this->id_resp = $id_resp;

        return $this;
    }

    public function getTipoMant(): ?string
    {
        return $this->tipo_mant;
    }

    public function setTipoMant(string $tipo_mant): self
    {
        $this->tipo_mant = $tipo_mant;

        return $this;
    }

    public function getFechaFact(): ?\DateTimeInterface
    {
        return $this->fecha_fact;
    }

    public function setFechaFact(\DateTimeInterface $fecha_fact): self
    {
        $this->fecha_fact = $fecha_fact;

        return $this;
    }

    public function getNroFact(): ?string
    {
        return $this->nro_fact;
    }

    public function setNroFact(string $nro_fact): self
    {
        $this->nro_fact = $nro_fact;

        return $this;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function setProveedor(string $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getProveedorRif(): ?string
    {
        return $this->proveedor_rif;
    }

    public function setProveedorRif(string $proveedor_rif): self
    {
        $this->proveedor_rif = $proveedor_rif;

        return $this;
    }

    public function getTelefonoProv(): ?string
    {
        return $this->telefono_prov;
    }

    public function setTelefonoProv(string $telefono_prov): self
    {
        $this->telefono_prov = $telefono_prov;

        return $this;
    }

    public function getMontoFact(): ?string
    {
        return $this->monto_fact;
    }

    public function setMontoFact(string $monto_fact): self
    {
        $this->monto_fact = $monto_fact;

        return $this;
    }

    public function getMontoIva(): ?string
    {
        return $this->monto_iva;
    }

    public function setMontoIva(string $monto_iva): self
    {
        $this->monto_iva = $monto_iva;

        return $this;
    }

    public function getTotalFactura(): ?string
    {
        return $this->total_factura;
    }

    public function setTotalFactura(string $total_factura): self
    {
        $this->total_factura = $total_factura;

        return $this;
    }

    public function getUnidadTiempo(): ?string
    {
        return $this->unidad_tiempo;
    }

    public function setUnidadTiempo(string $unidad_tiempo): self
    {
        $this->unidad_tiempo = $unidad_tiempo;

        return $this;
    }

    public function getNumeroTiempo(): ?int
    {
        return $this->numero_tiempo;
    }

    public function setNumeroTiempo(int $numero_tiempo): self
    {
        $this->numero_tiempo = $numero_tiempo;

        return $this;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(string $detalle): self
    {
        $this->detalle = $detalle;

        return $this;
    }

    public function getSiTraslado(): ?bool
    {
        return $this->si_traslado;
    }

    public function setSiTraslado(bool $si_traslado): self
    {
        $this->si_traslado = $si_traslado;

        return $this;
    }

    public function getCostoTraslado(): ?string
    {
        return $this->costo_traslado;
    }

    public function setCostoTraslado(string $costo_traslado): self
    {
        $this->costo_traslado = $costo_traslado;

        return $this;
    }

    public function getImpTraslado(): ?string
    {
        return $this->imp_traslado;
    }

    public function setImpTraslado(string $imp_traslado): self
    {
        $this->imp_traslado = $imp_traslado;

        return $this;
    }

    public function getTotalTraslado(): ?string
    {
        return $this->total_traslado;
    }

    public function setTotalTraslado(string $total_traslado): self
    {
        $this->total_traslado = $total_traslado;

        return $this;
    }

    public function getBanco(): ?string
    {
        return $this->banco;
    }

    public function setBanco(string $banco): self
    {
        $this->banco = $banco;

        return $this;
    }

    public function getTipoDoc(): ?string
    {
        return $this->tipo_doc;
    }

    public function setTipoDoc(string $tipo_doc): self
    {
        $this->tipo_doc = $tipo_doc;

        return $this;
    }

    public function getNumeroDoc(): ?string
    {
        return $this->numero_doc;
    }

    public function setNumeroDoc(string $numero_doc): self
    {
        $this->numero_doc = $numero_doc;

        return $this;
    }

    public function isSiTraslado(): ?bool
    {
        return $this->si_traslado;
    }
}
