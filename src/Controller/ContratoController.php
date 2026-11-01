<?php

namespace App\Controller;

use App\Manager\ContratoManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContratoController extends AbstractController
{
    // CU-11 Asignar propiedad a cliente (desde el detalle de la propiedad)
    #[Route('/propiedades/{id}/asignar', name: 'contrato_asignar', methods: ['POST'])]
    public function asignar(int $id, Request $request, ContratoManager $contratoManager): Response
    {
        try {
            $contrato = $contratoManager->asignar(
                $id,
                (int) $request->request->get('inquilino'),
                $request->request->get('fechaInicio', ''),
                $request->request->get('fechaFin', ''),
                (int) $request->request->get('precioMensual'),
            );
            $this->addFlash('exito', 'Contrato N° ' . $contrato->getId() . ' creado. La propiedad pasó a Alquilada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('propiedad_detalle', ['id' => $id]);
    }

    // CU-12 Cancelar contrato: listado (pasos 1-2)
    #[Route('/contratos', name: 'contrato_listar', methods: ['GET'])]
    public function listar(ContratoManager $contratoManager): Response
    {
        return $this->render('contrato/lista.html.twig', [
            'contratos' => $contratoManager->getContratos(),
        ]);
    }

    // CU-12 Cancelar contrato: pide confirmación
    #[Route('/contratos/{id}/cancelar', name: 'contrato_confirmar_cancelar', methods: ['GET'])]
    public function confirmarCancelar(int $id, ContratoManager $contratoManager): Response
    {
        try {
            $contrato = $contratoManager->getContrato($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('contrato_listar');
        }

        return $this->render('contrato/lista.html.twig', [
            'contratos' => $contratoManager->getContratos(),
            'cancelar' => $contrato,
        ]);
    }

    // CU-12 Cancelar contrato: confirma la cancelación
    #[Route('/contratos/{id}/cancelar', name: 'contrato_cancelar', methods: ['POST'])]
    public function cancelar(int $id, ContratoManager $contratoManager): Response
    {
        try {
            $contrato = $contratoManager->cancelar($id);
            $this->addFlash('exito', 'Contrato N° ' . $contrato->getId() . ' cancelado. La propiedad ' . $contrato->getPropiedad()->getDireccion() . ' volvió a Disponible.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('contrato_listar');
    }
}
