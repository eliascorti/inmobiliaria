<?php

namespace App\Controller;

use App\Manager\PropiedadManager;
use App\Manager\SolicitudManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Web pública: todas las rutas /catalogo se ven sin iniciar sesión (PUBLIC_ACCESS en security.yaml)
class PublicoController extends AbstractController
{
    // CU-17 Consultar propiedades disponibles: catálogo
    #[Route('/catalogo', name: 'publico_catalogo', methods: ['GET'])]
    public function catalogo(PropiedadManager $propiedadManager): Response
    {
        return $this->render('publico/catalogo.html.twig', [
            'propiedades' => $propiedadManager->getPropiedadesDisponibles(),
        ]);
    }

    // CU-17 Consultar propiedades disponibles: detalle (desde acá se solicita el alquiler)
    #[Route('/catalogo/{id}', name: 'publico_detalle', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function detalle(int $id, PropiedadManager $propiedadManager): Response
    {
        try {
            $propiedad = $propiedadManager->getPropiedadDisponible($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('publico_catalogo');
        }

        return $this->render('publico/detalle.html.twig', [
            'propiedad' => $propiedad,
        ]);
    }

    // CU-18 Solicitar alquiler
    #[Route('/catalogo/{id}/solicitar', name: 'publico_solicitar', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function solicitar(int $id, Request $request, SolicitudManager $solicitudManager): Response
    {
        try {
            $solicitud = $solicitudManager->registrar(
                $id,
                trim($request->request->get('nombre', '')),
                trim($request->request->get('apellido', '')),
                trim($request->request->get('dni', '')),
                trim($request->request->get('telefono', '')),
                trim($request->request->get('email', '')),
                $request->request->get('fechaInicio', ''),
                $request->request->get('fechaFin', ''),
            );
            $this->addFlash('exito', 'Recibimos tu solicitud N° ' . $solicitud->getId() . '. Guardá ese número para consultar cómo sigue.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('publico_detalle', ['id' => $id]);
        }

        return $this->redirectToRoute('publico_estado');
    }

    // CU-19 Consultar estado de solicitud: muestra el formulario vacío
    #[Route('/catalogo/estado', name: 'publico_estado', methods: ['GET'])]
    public function estado(): Response
    {
        return $this->render('publico/estado.html.twig');
    }

    // CU-19 Consultar estado de solicitud: busca con número y DNI.
    // Es POST para que el DNI no quede en la dirección ni en el historial del navegador.
    #[Route('/catalogo/estado', name: 'publico_consultar_estado', methods: ['POST'])]
    public function consultarEstado(Request $request, SolicitudManager $solicitudManager): Response
    {
        $numero = (int) $request->request->get('numero');
        $dni = trim($request->request->get('dni', ''));

        try {
            $solicitud = $solicitudManager->consultarEstado($numero, $dni);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('publico_estado');
        }

        // No cambia ningún dato, por eso muestra el resultado directamente (render, no redirect)
        return $this->render('publico/estado.html.twig', [
            'solicitud' => $solicitud,
        ]);
    }
}
