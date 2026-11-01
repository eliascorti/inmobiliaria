<?php

namespace App\DataFixtures;

use App\Entity\Cliente;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClienteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $datos = [
            ['Laura', 'Gómez', '28456123', '341 555-1201', 'laura.gomez@mail.com'],
            ['Martín', 'Pérez', '30112987', '341 555-3344', 'martin.perez@mail.com'],
            ['Sofía', 'Romero', '35789456', '341 555-7788', 'sofia.romero@mail.com'],
            ['Diego', 'Fernández', '27654321', '341 555-2090', 'diego.fernandez@mail.com'],
            ['Valentina', 'Ruiz', '40123654', '341 555-6512', 'valentina.ruiz@mail.com'],
            ['Javier', 'Morales', '33987120', '341 555-4471', 'javier.morales@mail.com'],
        ];

        foreach ($datos as $i => [$nombre, $apellido, $dni, $telefono, $email]) {
            $cliente = new Cliente();
            $cliente->setNombre($nombre);
            $cliente->setApellido($apellido);
            $cliente->setDni($dni);
            $cliente->setTelefono($telefono);
            $cliente->setEmail($email);

            $manager->persist($cliente);
            // La referencia permite usar este cliente en los otros fixtures
            $this->addReference('cliente-' . $i, $cliente);
        }

        $manager->flush();
    }
}
