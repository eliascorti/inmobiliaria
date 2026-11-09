<?php

namespace App\Manager;

use App\Entity\Contrato;
use App\Entity\Solicitud;
use App\Repository\ClienteRepository;
use App\Repository\SolicitudRepository;
use Doctrine\ORM\EntityManagerInterface;

class SolicitudManager
{
    public function __construct(
        private SolicitudRepository $solicitudRepository,
        private ClienteRepository $clienteRepository,
        private PropiedadManager $propiedadManager,
        private ClienteManager $clienteManager,
        private ContratoManager $contratoManager,
        private EntityManagerInterface $entityManager,
    ) {
    }

    // CU-14 Consultar solicitudes: por defecto solo las Pendientes; con $todas = true, también las resueltas.
    // Siempre las más nuevas primero (id más alto arriba).
    public function getSolicitudes(bool $todas = false): array
    {
        $filtro = $todas ? [] : ['estado' => Solicitud::PENDIENTE];

        return $this->solicitudRepository->findBy($filtro, ['id' => 'DESC']);
    }

    // Lo usan las pestañas del listado y el aviso del menú
    public function contarPendientes(): int
    {
        return $this->solicitudRepository->count(['estado' => Solicitud::PENDIENTE]);
    }

    public function getSolicitud(int $id): Solicitud
    {
        $solicitud = $this->solicitudRepository->find($id);
        if (!$solicitud) {
            throw new \Exception('La solicitud no existe.');
        }

        return $solicitud;
    }

    // CU-18 Solicitar alquiler (lo hace el Visitante, sin iniciar sesión)
    public function registrar(int $propiedadId, string $nombre, string $apellido, string $dni, string $telefono, string $email, string $fechaInicio, string $fechaFin): Solicitud
    {
        // RN-11: solo se puede solicitar una propiedad Disponible
        $propiedad = $this->propiedadManager->getPropiedadDisponible($propiedadId);

        // Los datos personales se validan igual que los de un cliente
        $this->clienteManager->validarDatos($nombre, $apellido, $dni, $telefono, $email);

        // RN-06: la fecha de fin debe ser posterior a la de inicio
        if ($fechaInicio === '' || $fechaFin === '') {
            throw new \Exception('Completá las fechas.');
        }
        $inicio = new \DateTime($fechaInicio);
        $fin = new \DateTime($fechaFin);
        if ($fin <= $inicio) {
            throw new \Exception('La fecha de fin debe ser posterior a la de inicio.');
        }

        $solicitud = new Solicitud();
        $solicitud->setPropiedad($propiedad);
        $solicitud->setNombre($nombre);
        $solicitud->setApellido($apellido);
        $solicitud->setDni($dni);
        $solicitud->setTelefono($telefono);
        $solicitud->setEmail($email);
        $solicitud->setFechaInicio($inicio);
        $solicitud->setFechaFin($fin);
        $solicitud->setEstado(Solicitud::PENDIENTE);

        $this->entityManager->persist($solicitud);
        $this->entityManager->flush();

        return $solicitud;
    }

    // CU-19 Consultar estado de solicitud: hacen falta el número y el DNI
    public function consultarEstado(int $numero, string $dni): Solicitud
    {
        $solicitud = $this->solicitudRepository->findOneBy(['id' => $numero, 'dni' => $dni]);
        if (!$solicitud) {
            throw new \Exception('No encontramos una solicitud con ese número y DNI.');
        }

        return $solicitud;
    }

    // CU-15 Aprobar solicitud: incluye CU-11 Asignar propiedad a cliente
    public function aprobar(int $id): Contrato
    {
        $solicitud = $this->getSolicitud($id);

        // RN-13: solo se aprueba una solicitud Pendiente
        if (!$solicitud->estaPendiente()) {
            throw new \Exception('La solicitud N° ' . $id . ' ya fue resuelta.');
        }

        // RN-04: se controla antes de registrar al cliente
        $propiedad = $solicitud->getPropiedad();
        if (!$propiedad->estaDisponible()) {
            throw new \Exception('La propiedad ya está alquilada.');
        }

        // RN-12: si ya existe un cliente con ese DNI se usa; si no, se registra
        $cliente = $this->clienteRepository->findOneBy(['dni' => $solicitud->getDni()]);
        if (!$cliente) {
            $cliente = $this->clienteManager->registrar(
                $solicitud->getNombre(),
                $solicitud->getApellido(),
                $solicitud->getDni(),
                $solicitud->getTelefono(),
                $solicitud->getEmail(),
            );
        }

        $solicitud->aprobar();

        // «include»: se reutiliza Asignar propiedad. Su flush guarda también el nuevo estado de la solicitud
        return $this->contratoManager->asignar(
            $propiedad->getId(),
            $cliente->getId(),
            $solicitud->getFechaInicio()->format('Y-m-d'),
            $solicitud->getFechaFin()->format('Y-m-d'),
            $propiedad->getPrecio(),
        );
    }

    // CU-16 Rechazar solicitud
    public function rechazar(int $id): Solicitud
    {
        $solicitud = $this->getSolicitud($id);

        // RN-13: solo se rechaza una solicitud Pendiente
        if (!$solicitud->estaPendiente()) {
            throw new \Exception('La solicitud N° ' . $id . ' ya fue resuelta.');
        }

        $solicitud->rechazar();
        $this->entityManager->flush();

        return $solicitud;
    }
}
