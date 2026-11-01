<?php

namespace App\Manager;

use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClienteManager
{
    public function __construct(
        private ClienteRepository $clienteRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    // CU-03 Consultar clientes
    public function getClientes(): array
    {
        return $this->clienteRepository->findBy([], ['apellido' => 'ASC']);
    }

    public function getCliente(int $id): Cliente
    {
        $cliente = $this->clienteRepository->find($id);
        if (!$cliente) {
            throw new \Exception('El cliente no existe.');
        }

        return $cliente;
    }

    // CU-04 Registrar cliente
    public function registrar(string $nombre, string $apellido, string $dni, string $telefono, string $email): Cliente
    {
        $this->validarDatos($nombre, $apellido, $dni, $telefono, $email);

        // RN-01: el DNI no puede repetirse
        $existente = $this->clienteRepository->findOneBy(['dni' => $dni]);
        if ($existente) {
            throw new \Exception('Ya existe un cliente con el DNI ' . $dni . '.');
        }

        $cliente = new Cliente();
        $cliente->setNombre($nombre);
        $cliente->setApellido($apellido);
        $cliente->setDni($dni);
        $cliente->setTelefono($telefono);
        $cliente->setEmail($email);

        $this->entityManager->persist($cliente);
        $this->entityManager->flush();

        return $cliente;
    }

    // CU-05 Modificar cliente
    public function modificar(int $id, string $nombre, string $apellido, string $dni, string $telefono, string $email): Cliente
    {
        $cliente = $this->getCliente($id);
        $this->validarDatos($nombre, $apellido, $dni, $telefono, $email);

        // RN-01: el DNI no puede pertenecer a otro cliente
        $existente = $this->clienteRepository->findOneBy(['dni' => $dni]);
        if ($existente && $existente !== $cliente) {
            throw new \Exception('El DNI ' . $dni . ' pertenece a otro cliente.');
        }

        $cliente->setNombre($nombre);
        $cliente->setApellido($apellido);
        $cliente->setDni($dni);
        $cliente->setTelefono($telefono);
        $cliente->setEmail($email);

        // No hace falta persist: Doctrine ya conoce el objeto
        $this->entityManager->flush();

        return $cliente;
    }

    // CU-06 Eliminar cliente
    public function eliminar(int $id): void
    {
        $cliente = $this->getCliente($id);

        // RN-08: no se puede eliminar si tiene propiedades o contratos
        if (!$cliente->puedeEliminarse()) {
            throw new \Exception('No se puede eliminar a ' . $cliente->getNombreCompleto() . ': tiene propiedades o contratos asociados.');
        }

        $this->entityManager->remove($cliente);
        $this->entityManager->flush();
    }

    private function validarDatos(string $nombre, string $apellido, string $dni, string $telefono, string $email): void
    {
        if ($nombre === '' || $apellido === '' || $dni === '' || $telefono === '' || $email === '') {
            throw new \Exception('Todos los campos son obligatorios.');
        }
        if (!ctype_digit($dni)) {
            throw new \Exception('El DNI debe tener solo números.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('El email no tiene un formato válido.');
        }
    }
}
