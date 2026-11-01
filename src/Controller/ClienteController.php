<?php

namespace App\Controller;

use App\Manager\ClienteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClienteController extends AbstractController
{
    // CU-03 Consultar clientes
    #[Route('/clientes', name: 'cliente_listar', methods: ['GET'])]
    public function listar(ClienteManager $clienteManager): Response
    {
        return $this->render('cliente/lista.html.twig', [
            'clientes' => $clienteManager->getClientes(),
        ]);
    }

    // CU-04 Registrar cliente
    #[Route('/clientes/nuevo', name: 'cliente_nuevo', methods: ['POST'])]
    public function nuevo(Request $request, ClienteManager $clienteManager): Response
    {
        try {
            $clienteManager->registrar(
                trim($request->request->get('nombre', '')),
                trim($request->request->get('apellido', '')),
                trim($request->request->get('dni', '')),
                trim($request->request->get('telefono', '')),
                trim($request->request->get('email', '')),
            );
            $this->addFlash('exito', 'Cliente registrado.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cliente_listar');
    }

    // CU-05 Modificar cliente: abre el modal con los datos actuales
    #[Route('/clientes/{id}/editar', name: 'cliente_editar', methods: ['GET'])]
    public function editar(int $id, ClienteManager $clienteManager): Response
    {
        try {
            $cliente = $clienteManager->getCliente($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('cliente_listar');
        }

        return $this->render('cliente/lista.html.twig', [
            'clientes' => $clienteManager->getClientes(),
            'editar' => $cliente,
        ]);
    }

    // CU-05 Modificar cliente: guarda los cambios
    #[Route('/clientes/{id}/editar', name: 'cliente_modificar', methods: ['POST'])]
    public function modificar(int $id, Request $request, ClienteManager $clienteManager): Response
    {
        try {
            $clienteManager->modificar(
                $id,
                trim($request->request->get('nombre', '')),
                trim($request->request->get('apellido', '')),
                trim($request->request->get('dni', '')),
                trim($request->request->get('telefono', '')),
                trim($request->request->get('email', '')),
            );
            $this->addFlash('exito', 'Cliente modificado.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cliente_listar');
    }

    // CU-06 Eliminar cliente: pide confirmación
    #[Route('/clientes/{id}/eliminar', name: 'cliente_confirmar_eliminar', methods: ['GET'])]
    public function confirmarEliminar(int $id, ClienteManager $clienteManager): Response
    {
        try {
            $cliente = $clienteManager->getCliente($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('cliente_listar');
        }

        return $this->render('cliente/lista.html.twig', [
            'clientes' => $clienteManager->getClientes(),
            'eliminar' => $cliente,
        ]);
    }

    // CU-06 Eliminar cliente: confirma la baja
    #[Route('/clientes/{id}/eliminar', name: 'cliente_eliminar', methods: ['POST'])]
    public function eliminar(int $id, ClienteManager $clienteManager): Response
    {
        try {
            $clienteManager->eliminar($id);
            $this->addFlash('exito', 'Cliente eliminado.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cliente_listar');
    }
}
