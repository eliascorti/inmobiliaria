<?php

namespace App\DataFixtures;

use App\Entity\Propiedad;
use App\Entity\Solicitud;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SolicitudFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // [nombre, apellido, dni, teléfono, email, índice de la propiedad, inicio, fin, estado]
        // La primera es de una persona nueva y la segunda de un cliente que ya existe (mismo DNI)
        $datos = [
            ['Lucía', 'Herrera', '41256789', '341 555-8120', 'lucia.herrera@mail.com', 4, '2026-12-01', '2028-11-30', Solicitud::PENDIENTE],
            ['Martín', 'Pérez', '30112987', '341 555-3344', 'martin.perez@mail.com', 1, '2027-01-01', '2028-12-31', Solicitud::PENDIENTE],
            ['Tomás', 'Acosta', '38441902', '341 555-9034', 'tomas.acosta@mail.com', 3, '2026-12-15', '2027-12-14', Solicitud::RECHAZADA],
        ];

        foreach ($datos as [$nombre, $apellido, $dni, $telefono, $email, $propiedad, $inicio, $fin, $estado]) {
            $solicitud = new Solicitud();
            $solicitud->setNombre($nombre);
            $solicitud->setApellido($apellido);
            $solicitud->setDni($dni);
            $solicitud->setTelefono($telefono);
            $solicitud->setEmail($email);
            $solicitud->setPropiedad($this->getReference('propiedad-' . $propiedad, Propiedad::class));
            $solicitud->setFechaInicio(new \DateTime($inicio));
            $solicitud->setFechaFin(new \DateTime($fin));
            $solicitud->setEstado($estado);

            $manager->persist($solicitud);
        }

        $manager->flush();
    }

    // Se carga después de los contratos: así las propiedades ya tienen su estado final
    public function getDependencies(): array
    {
        return [ContratoFixtures::class];
    }
}
