<?php

namespace App\Entity;

use App\Repository\PropiedadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropiedadRepository::class)]
class Propiedad
{
    public const DISPONIBLE = 'Disponible';
    public const ALQUILADA = 'Alquilada';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $direccion = null;

    #[ORM\Column(length: 30)]
    private ?string $tipo = null;

    #[ORM\Column]
    private ?int $ambientes = null;

    #[ORM\Column]
    private ?int $precio = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\ManyToOne(inversedBy: 'propiedades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cliente $propietario = null;

    /**
     * @var Collection<int, Contrato>
     */
    #[ORM\OneToMany(targetEntity: Contrato::class, mappedBy: 'propiedad')]
    private Collection $contratos;

    /**
     * @var Collection<int, Solicitud>
     */
    #[ORM\OneToMany(targetEntity: Solicitud::class, mappedBy: 'propiedad')]
    private Collection $solicitudes;

    public function __construct()
    {
        $this->contratos = new ArrayCollection();
        $this->solicitudes = new ArrayCollection();
    }

    public function estaDisponible(): bool
    {
        return $this->estado === self::DISPONIBLE;
    }

    // RN-04: al asignarla a un cliente pasa a Alquilada
    public function alquilar(): void
    {
        $this->estado = self::ALQUILADA;
    }

    // RN-07: al cancelar su contrato vuelve a Disponible
    public function liberar(): void
    {
        $this->estado = self::DISPONIBLE;
    }

    // RN-09: solo se puede eliminar si no tiene contratos ni solicitudes
    public function puedeEliminarse(): bool
    {
        return $this->contratos->isEmpty() && $this->solicitudes->isEmpty();
    }

    public function getContratoVigente(): ?Contrato
    {
        foreach ($this->contratos as $contrato) {
            if ($contrato->estaVigente()) {
                return $contrato;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getAmbientes(): ?int
    {
        return $this->ambientes;
    }

    public function setAmbientes(int $ambientes): static
    {
        $this->ambientes = $ambientes;

        return $this;
    }

    public function getPrecio(): ?int
    {
        return $this->precio;
    }

    public function setPrecio(int $precio): static
    {
        $this->precio = $precio;

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

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getPropietario(): ?Cliente
    {
        return $this->propietario;
    }

    public function setPropietario(?Cliente $propietario): static
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * @return Collection<int, Contrato>
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): static
    {
        if (!$this->contratos->contains($contrato)) {
            $this->contratos->add($contrato);
            $contrato->setPropiedad($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): static
    {
        if ($this->contratos->removeElement($contrato)) {
            // set the owning side to null (unless already changed)
            if ($contrato->getPropiedad() === $this) {
                $contrato->setPropiedad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Solicitud>
     */
    public function getSolicitudes(): Collection
    {
        return $this->solicitudes;
    }

    public function addSolicitud(Solicitud $solicitud): static
    {
        if (!$this->solicitudes->contains($solicitud)) {
            $this->solicitudes->add($solicitud);
            $solicitud->setPropiedad($this);
        }

        return $this;
    }

    public function removeSolicitud(Solicitud $solicitud): static
    {
        if ($this->solicitudes->removeElement($solicitud)) {
            // set the owning side to null (unless already changed)
            if ($solicitud->getPropiedad() === $this) {
                $solicitud->setPropiedad(null);
            }
        }

        return $this;
    }
}
