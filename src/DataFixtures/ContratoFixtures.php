<?php

namespace App\DataFixtures;

use App\Entity\Cliente;
use App\Entity\Contrato;
use App\Entity\Propiedad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContratoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // [índice de la propiedad, índice del inquilino, inicio, fin, precio, vigente]
        $datos = [
            [0, 1, '2026-03-01', '2028-02-29', 350000, true],
            [2, 2, '2026-06-01', '2028-05-31', 240000, true],
            [1, 4, '2025-02-01', '2027-01-31', 500000, false],
        ];

        foreach ($datos as [$propiedad, $inquilino, $inicio, $fin, $precio, $vigente]) {
            $contrato = new Contrato();
            $contrato->setPropiedad($this->getReference('propiedad-' . $propiedad, Propiedad::class));
            $contrato->setInquilino($this->getReference('cliente-' . $inquilino, Cliente::class));
            $contrato->setFechaInicio(new \DateTime($inicio));
            $contrato->setFechaFin(new \DateTime($fin));
            $contrato->setPrecioMensual($precio);
            $contrato->setEstado($vigente ? Contrato::VIGENTE : Contrato::CANCELADO);

            // Un contrato vigente deja la propiedad Alquilada (RN-04)
            if ($vigente) {
                $contrato->getPropiedad()->alquilar();
            }

            $manager->persist($contrato);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PropiedadFixtures::class, ClienteFixtures::class];
    }
}
