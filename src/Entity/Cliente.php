<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $apellido = null;

    // RN-01: el DNI identifica de forma única a cada cliente
    #[ORM\Column(length: 20, unique: true)]
    private ?string $dni = null;

    #[ORM\Column(length: 30)]
    private ?string $telefono = null;

    #[ORM\Column(length: 150)]
    private ?string $email = null;

    /**
     * @var Collection<int, Propiedad>
     */
    #[ORM\OneToMany(targetEntity: Propiedad::class, mappedBy: 'propietario')]
    private Collection $propiedades;

    /**
     * @var Collection<int, Contrato>
     */
    #[ORM\OneToMany(targetEntity: Contrato::class, mappedBy: 'inquilino')]
    private Collection $contratos;

    public function __construct()
    {
        $this->propiedades = new ArrayCollection();
        $this->contratos = new ArrayCollection();
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    // RN-08: solo se puede eliminar si no tiene propiedades ni contratos
    public function puedeEliminarse(): bool
    {
        return $this->propiedades->isEmpty() && $this->contratos->isEmpty();
    }

    // El rol no se guarda: se deduce de sus propiedades y de sus contratos vigentes
    public function getRol(): string
    {
        $esPropietario = !$this->propiedades->isEmpty();
        $esInquilino = false;
        foreach ($this->contratos as $contrato) {
            if ($contrato->estaVigente()) {
                $esInquilino = true;
            }
        }

        if ($esPropietario && $esInquilino) {
            return 'Propietario e inquilino';
        }
        if ($esPropietario) {
            return 'Propietario';
        }
        if ($esInquilino) {
            return 'Inquilino';
        }

        return 'Sin operaciones';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Propiedad>
     */
    public function getPropiedades(): Collection
    {
        return $this->propiedades;
    }

    public function addPropiedade(Propiedad $propiedade): static
    {
        if (!$this->propiedades->contains($propiedade)) {
            $this->propiedades->add($propiedade);
            $propiedade->setPropietario($this);
        }

        return $this;
    }

    public function removePropiedade(Propiedad $propiedade): static
    {
        if ($this->propiedades->removeElement($propiedade)) {
            // set the owning side to null (unless already changed)
            if ($propiedade->getPropietario() === $this) {
                $propiedade->setPropietario(null);
            }
        }

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
            $contrato->setInquilino($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): static
    {
        if ($this->contratos->removeElement($contrato)) {
            // set the owning side to null (unless already changed)
            if ($contrato->getInquilino() === $this) {
                $contrato->setInquilino(null);
            }
        }

        return $this;
    }
}
