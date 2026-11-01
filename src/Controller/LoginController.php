<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    // CU-01 Iniciar sesión: muestra el formulario.
    // El envío (POST /login) lo procesa el autenticador form_login del firewall.
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $ultimoEmail = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'error' => $error,
            'ultimoEmail' => $ultimoEmail,
        ]);
    }

    // CU-02 Cerrar sesión: nunca se ejecuta, el firewall intercepta /logout.
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('El firewall intercepta esta ruta.');
    }
}
