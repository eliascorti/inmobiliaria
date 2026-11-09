<?php

namespace App\DataFixtures;

use App\Entity\Cliente;
use App\Entity\Propiedad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PropiedadFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $directorioImagenes)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // [dirección, tipo, ambientes, precio, índice del propietario, imagen de ejemplo]
        // Dos de cada tipo. Las fotos son de Unsplash (ver imagenes/CREDITOS.txt)
        $datos = [
            ['Av. Pellegrini 1250', 'Departamento', 2, 350000, 0, 'pellegrini-1250.jpg'],
            ['Bv. Oroño 845', 'Casa', 4, 520000, 0, 'orono-845.jpg'],
            ['San Martín 2210', 'Departamento', 1, 240000, 3, 'san-martin-2210.jpg'],
            ['Mitre 1530', 'Local', 1, 410000, 3, 'mitre-1530.jpg'],
            ['Córdoba 3120', 'Casa', 3, 460000, 5, 'cordoba-3120.jpg'],
            ['Rioja 1890', 'Local', 2, 690000, 5, 'rioja-1890.jpg'],
        ];

        if (!is_dir($this->directorioImagenes)) {
            mkdir($this->directorioImagenes, 0777, true);
        }

        foreach ($datos as $i => [$direccion, $tipo, $ambientes, $precio, $propietario, $imagen]) {
            // Copia la imagen de ejemplo a la carpeta pública de imágenes
            copy(__DIR__ . '/imagenes/' . $imagen, $this->directorioImagenes . '/' . $imagen);

            $propiedad = new Propiedad();
            $propiedad->setDireccion($direccion);
            $propiedad->setTipo($tipo);
            $propiedad->setAmbientes($ambientes);
            $propiedad->setPrecio($precio);
            $propiedad->setPropietario($this->getReference('cliente-' . $propietario, Cliente::class));
            $propiedad->setEstado(Propiedad::DISPONIBLE);
            $propiedad->setImagen($imagen);

            $manager->persist($propiedad);
            $this->addReference('propiedad-' . $i, $propiedad);
        }

        $manager->flush();
    }

    // Los clientes se cargan antes que las propiedades
    public function getDependencies(): array
    {
        return [ClienteFixtures::class];
    }
}
