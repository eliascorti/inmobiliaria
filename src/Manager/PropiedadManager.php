<?php

namespace App\Manager;

use App\Entity\Propiedad;
use App\Repository\ClienteRepository;
use App\Repository\PropiedadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PropiedadManager
{
    public const TIPOS = ['Casa', 'Departamento', 'Local'];

    public function __construct(
        private PropiedadRepository $propiedadRepository,
        private ClienteRepository $clienteRepository,
        private EntityManagerInterface $entityManager,
        private string $directorioImagenes,
    ) {
    }

    // CU-07 Consultar propiedades
    public function getPropiedades(): array
    {
        return $this->propiedadRepository->findBy([], ['direccion' => 'ASC']);
    }

    public function getPropiedad(int $id): Propiedad
    {
        $propiedad = $this->propiedadRepository->find($id);
        if (!$propiedad) {
            throw new \Exception('La propiedad no existe.');
        }

        return $propiedad;
    }

    // CU-17 Consultar propiedades disponibles (web pública): solo las Disponibles
    public function getPropiedadesDisponibles(): array
    {
        return $this->propiedadRepository->findBy(['estado' => Propiedad::DISPONIBLE], ['direccion' => 'ASC']);
    }

    public function getPropiedadDisponible(int $id): Propiedad
    {
        $propiedad = $this->propiedadRepository->find($id);
        if (!$propiedad || !$propiedad->estaDisponible()) {
            throw new \Exception('La propiedad no está disponible.');
        }

        return $propiedad;
    }

    // CU-08 Registrar propiedad
    public function registrar(string $direccion, string $tipo, int $ambientes, int $precio, int $propietarioId, ?UploadedFile $imagen): Propiedad
    {
        $this->validarDatos($direccion, $tipo, $ambientes, $precio);

        // RN-02: el propietario debe ser un cliente registrado
        $propietario = $this->clienteRepository->find($propietarioId);
        if (!$propietario) {
            throw new \Exception('Elegí el propietario de la propiedad.');
        }
        if (!$imagen) {
            throw new \Exception('Elegí una imagen de la propiedad.');
        }

        $propiedad = new Propiedad();
        $propiedad->setDireccion($direccion);
        $propiedad->setTipo($tipo);
        $propiedad->setAmbientes($ambientes);
        $propiedad->setPrecio($precio);
        $propiedad->setPropietario($propietario);
        $propiedad->setEstado(Propiedad::DISPONIBLE); // RN-03
        $propiedad->setImagen($this->guardarImagen($imagen));

        $this->entityManager->persist($propiedad);
        $this->entityManager->flush();

        return $propiedad;
    }

    // CU-09 Modificar propiedad
    public function modificar(int $id, string $direccion, string $tipo, int $ambientes, int $precio, int $propietarioId, ?UploadedFile $imagen): Propiedad
    {
        $propiedad = $this->getPropiedad($id);
        $this->validarDatos($direccion, $tipo, $ambientes, $precio);

        $propietario = $this->clienteRepository->find($propietarioId);
        if (!$propietario) {
            throw new \Exception('Elegí el propietario de la propiedad.');
        }

        $propiedad->setDireccion($direccion);
        $propiedad->setTipo($tipo);
        $propiedad->setAmbientes($ambientes);
        $propiedad->setPrecio($precio);
        $propiedad->setPropietario($propietario);

        // Si no se elige una imagen nueva, se mantiene la actual
        if ($imagen) {
            $anterior = $propiedad->getImagen();
            $propiedad->setImagen($this->guardarImagen($imagen));
            $this->borrarImagen($anterior);
        }

        $this->entityManager->flush();

        return $propiedad;
    }

    // CU-10 Eliminar propiedad
    public function eliminar(int $id): void
    {
        $propiedad = $this->getPropiedad($id);

        // RN-09: no se puede eliminar si tiene contratos o solicitudes
        if (!$propiedad->puedeEliminarse()) {
            throw new \Exception('No se puede eliminar ' . $propiedad->getDireccion() . ': tiene contratos o solicitudes asociados.');
        }

        $this->borrarImagen($propiedad->getImagen());
        $this->entityManager->remove($propiedad);
        $this->entityManager->flush();
    }

    private function validarDatos(string $direccion, string $tipo, int $ambientes, int $precio): void
    {
        if ($direccion === '' || !in_array($tipo, self::TIPOS)) {
            throw new \Exception('Completá la dirección y el tipo de la propiedad.');
        }
        // RN-10
        if ($ambientes < 1) {
            throw new \Exception('La propiedad debe tener al menos un ambiente.');
        }
        if ($precio <= 0) {
            throw new \Exception('El precio debe ser mayor a cero.');
        }
    }

    // RNF-07: solo JPG, PNG o WEBP, guardadas en public/uploads/propiedades
    private function guardarImagen(UploadedFile $imagen): string
    {
        $extension = $imagen->guessExtension();
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            throw new \Exception('La imagen debe ser JPG, PNG o WEBP.');
        }

        $nombre = uniqid('propiedad_') . '.' . $extension;
        $imagen->move($this->directorioImagenes, $nombre);

        return $nombre;
    }

    private function borrarImagen(?string $nombre): void
    {
        if ($nombre && file_exists($this->directorioImagenes . '/' . $nombre)) {
            unlink($this->directorioImagenes . '/' . $nombre);
        }
    }
}
