<?php

namespace App\Entity;

use App\Repository\ContratoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratoRepository::class)]
class Contrato
{
    public const VIGENTE = 'Vigente';
    public const CANCELADO = 'Cancelado';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaInicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaFin = null;

    #[ORM\Column]
    private ?int $precioMensual = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\ManyToOne(inversedBy: 'contratos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Propiedad $propiedad = null;

    #[ORM\ManyToOne(inversedBy: 'contratos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cliente $inquilino = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function estaVigente(): bool
    {
        return $this->estado === self::VIGENTE;
    }

    // RN-07: el contrato pasa a Cancelado y la propiedad vuelve a Disponible
    public function cancelar(): void
    {
        $this->estado = self::CANCELADO;
        $this->propiedad->liberar();
    }

    public function getFechaInicio(): ?\DateTime
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTime $fechaInicio): static
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTime
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTime $fechaFin): static
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    public function getPrecioMensual(): ?int
    {
        return $this->precioMensual;
    }

    public function setPrecioMensual(int $precioMensual): static
    {
        $this->precioMensual = $precioMensual;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getPropiedad(): ?Propiedad
    {
        return $this->propiedad;
    }

    public function setPropiedad(?Propiedad $propiedad): static
    {
        $this->propiedad = $propiedad;

        return $this;
    }

    public function getInquilino(): ?Cliente
    {
        return $this->inquilino;
    }

    public function setInquilino(?Cliente $inquilino): static
    {
        $this->inquilino = $inquilino;

        return $this;
    }
}
