<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsuarioFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    // Usuario de prueba para entrar al sistema (CU-01)
    public function load(ObjectManager $manager): void
    {
        $usuario = new Usuario();
        $usuario->setEmail('admin@inmobiliaria.com');
        $usuario->setNombre('Administrador');
        // RNF-02: la contraseña se guarda con hash
        $usuario->setPassword($this->passwordHasher->hashPassword($usuario, 'admin123'));

        $manager->persist($usuario);
        $manager->flush();
    }
}
