<?php

namespace App\Controller;

use App\Manager\SolicitudManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SolicitudController extends AbstractController
{
    // CU-14 Consultar solicitudes. El filtro llega en la dirección: /solicitudes?ver=todas
    // ($request->query lee la URL; $request->request lee un formulario POST)
    #[Route('/solicitudes', name: 'solicitud_listar', methods: ['GET'])]
    public function listar(Request $request, SolicitudManager $solicitudManager): Response
    {
        $todas = $request->query->get('ver') === 'todas';

        return $this->render('solicitud/lista.html.twig', [
            'solicitudes' => $solicitudManager->getSolicitudes($todas),
            'todas' => $todas,
            'pendientes' => $solicitudManager->contarPendientes(),
        ]);
    }

    // CU-15 Aprobar solicitud
    #[Route('/solicitudes/{id}/aprobar', name: 'solicitud_aprobar', methods: ['POST'])]
    public function aprobar(int $id, SolicitudManager $solicitudManager): Response
    {
        try {
            $contrato = $solicitudManager->aprobar($id);
            $this->addFlash('exito', 'Solicitud N° ' . $id . ' aprobada. Se creó el contrato N° ' . $contrato->getId() . ' y la propiedad pasó a Alquilada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solicitud_listar');
    }

    // CU-16 Rechazar solicitud: pide confirmación
    #[Route('/solicitudes/{id}/rechazar', name: 'solicitud_confirmar_rechazar', methods: ['GET'])]
    public function confirmarRechazar(int $id, SolicitudManager $solicitudManager): Response
    {
        try {
            $solicitud = $solicitudManager->getSolicitud($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('solicitud_listar');
        }

        return $this->render('solicitud/lista.html.twig', [
            'solicitudes' => $solicitudManager->getSolicitudes(),
            'todas' => false,
            'pendientes' => $solicitudManager->contarPendientes(),
            'rechazar' => $solicitud,
        ]);
    }

    // CU-16 Rechazar solicitud: confirma
    #[Route('/solicitudes/{id}/rechazar', name: 'solicitud_rechazar', methods: ['POST'])]
    public function rechazar(int $id, SolicitudManager $solicitudManager): Response
    {
        try {
            $solicitudManager->rechazar($id);
            $this->addFlash('exito', 'Solicitud N° ' . $id . ' rechazada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solicitud_listar');
    }
}
