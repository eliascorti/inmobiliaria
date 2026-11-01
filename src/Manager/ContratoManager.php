<?php

namespace App\Manager;

use App\Entity\Contrato;
use App\Repository\ClienteRepository;
use App\Repository\ContratoRepository;
use App\Repository\PropiedadRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContratoManager
{
    public function __construct(
        private ContratoRepository $contratoRepository,
        private PropiedadRepository $propiedadRepository,
        private ClienteRepository $clienteRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    // CU-12: listado de contratos (paso 1-2)
    public function getContratos(): array
    {
        return $this->contratoRepository->findBy([], ['id' => 'DESC']);
    }

    public function getContrato(int $id): Contrato
    {
        $contrato = $this->contratoRepository->find($id);
        if (!$contrato) {
            throw new \Exception('El contrato no existe.');
        }

        return $contrato;
    }

    // CU-11 Asignar propiedad a cliente
    public function asignar(int $propiedadId, int $inquilinoId, string $fechaInicio, string $fechaFin, int $precioMensual): Contrato
    {
        $propiedad = $this->propiedadRepository->find($propiedadId);
        if (!$propiedad) {
            throw new \Exception('La propiedad no existe.');
        }

        // RN-04: solo se asigna una propiedad Disponible
        if (!$propiedad->estaDisponible()) {
            throw new \Exception('La propiedad ya está alquilada.');
        }

        $inquilino = $this->clienteRepository->find($inquilinoId);
        if (!$inquilino) {
            throw new \Exception('Elegí el inquilino.');
        }

        // RN-05: el inquilino no puede ser el propietario
        if ($inquilino === $propiedad->getPropietario()) {
            throw new \Exception('El inquilino no puede ser el propietario de la propiedad.');
        }

        // RN-06: fechas coherentes y precio mayor a cero
        if ($fechaInicio === '' || $fechaFin === '') {
            throw new \Exception('Completá las fechas del contrato.');
        }
        $inicio = new \DateTime($fechaInicio);
        $fin = new \DateTime($fechaFin);
        if ($fin <= $inicio) {
            throw new \Exception('La fecha de fin debe ser posterior a la de inicio.');
        }
        if ($precioMensual <= 0) {
            throw new \Exception('El precio mensual debe ser mayor a cero.');
        }

        $contrato = new Contrato();
        $contrato->setPropiedad($propiedad);
        $contrato->setInquilino($inquilino);
        $contrato->setFechaInicio($inicio);
        $contrato->setFechaFin($fin);
        $contrato->setPrecioMensual($precioMensual);
        $contrato->setEstado(Contrato::VIGENTE);

        $propiedad->alquilar();

        // RNF-05: un solo flush guarda el contrato y el nuevo estado de la propiedad
        $this->entityManager->persist($contrato);
        $this->entityManager->flush();

        return $contrato;
    }

    // CU-12 Cancelar contrato
    public function cancelar(int $id): Contrato
    {
        $contrato = $this->getContrato($id);

        // RN-07: un contrato cancelado no se reactiva ni se vuelve a cancelar
        if (!$contrato->estaVigente()) {
            throw new \Exception('El contrato N° ' . $contrato->getId() . ' ya está cancelado.');
        }

        $contrato->cancelar();
        $this->entityManager->flush();

        return $contrato;
    }
}
