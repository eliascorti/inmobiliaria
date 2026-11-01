<?php

namespace App\Controller;

use App\Manager\ClienteManager;
use App\Manager\PropiedadManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PropiedadController extends AbstractController
{
    // CU-07 Consultar propiedades: catálogo con imágenes
    #[Route('/propiedades', name: 'propiedad_listar', methods: ['GET'])]
    public function listar(PropiedadManager $propiedadManager, ClienteManager $clienteManager): Response
    {
        return $this->render('propiedad/lista.html.twig', [
            'propiedades' => $propiedadManager->getPropiedades(),
            'clientes' => $clienteManager->getClientes(),
            'tipos' => PropiedadManager::TIPOS,
        ]);
    }

    // CU-07 Consultar propiedades: detalle de una propiedad
    #[Route('/propiedades/{id}', name: 'propiedad_detalle', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function detalle(int $id, PropiedadManager $propiedadManager, ClienteManager $clienteManager): Response
    {
        try {
            $propiedad = $propiedadManager->getPropiedad($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('propiedad_listar');
        }

        return $this->render('propiedad/detalle.html.twig', [
            'propiedad' => $propiedad,
            'clientes' => $clienteManager->getClientes(),
        ]);
    }

    // CU-08 Registrar propiedad
    #[Route('/propiedades/nueva', name: 'propiedad_nueva', methods: ['POST'])]
    public function nueva(Request $request, PropiedadManager $propiedadManager): Response
    {
        try {
            $propiedadManager->registrar(
                trim($request->request->get('direccion', '')),
                $request->request->get('tipo', ''),
                (int) $request->request->get('ambientes'),
                (int) $request->request->get('precio'),
                (int) $request->request->get('propietario'),
                $request->files->get('imagen'),
            );
            $this->addFlash('exito', 'Propiedad registrada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('propiedad_listar');
    }

    // CU-09 Modificar propiedad: abre el modal con los datos actuales
    #[Route('/propiedades/{id}/editar', name: 'propiedad_editar', methods: ['GET'])]
    public function editar(int $id, PropiedadManager $propiedadManager, ClienteManager $clienteManager): Response
    {
        try {
            $propiedad = $propiedadManager->getPropiedad($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('propiedad_listar');
        }

        return $this->render('propiedad/lista.html.twig', [
            'propiedades' => $propiedadManager->getPropiedades(),
            'clientes' => $clienteManager->getClientes(),
            'tipos' => PropiedadManager::TIPOS,
            'editar' => $propiedad,
        ]);
    }

    // CU-09 Modificar propiedad: guarda los cambios
    #[Route('/propiedades/{id}/editar', name: 'propiedad_modificar', methods: ['POST'])]
    public function modificar(int $id, Request $request, PropiedadManager $propiedadManager): Response
    {
        try {
            $propiedadManager->modificar(
                $id,
                trim($request->request->get('direccion', '')),
                $request->request->get('tipo', ''),
                (int) $request->request->get('ambientes'),
                (int) $request->request->get('precio'),
                (int) $request->request->get('propietario'),
                $request->files->get('imagen'),
            );
            $this->addFlash('exito', 'Propiedad modificada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('propiedad_listar');
    }

    // CU-10 Eliminar propiedad: pide confirmación
    #[Route('/propiedades/{id}/eliminar', name: 'propiedad_confirmar_eliminar', methods: ['GET'])]
    public function confirmarEliminar(int $id, PropiedadManager $propiedadManager, ClienteManager $clienteManager): Response
    {
        try {
            $propiedad = $propiedadManager->getPropiedad($id);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('propiedad_listar');
        }

        return $this->render('propiedad/lista.html.twig', [
            'propiedades' => $propiedadManager->getPropiedades(),
            'clientes' => $clienteManager->getClientes(),
            'tipos' => PropiedadManager::TIPOS,
            'eliminar' => $propiedad,
        ]);
    }

    // CU-10 Eliminar propiedad: confirma la baja
    #[Route('/propiedades/{id}/eliminar', name: 'propiedad_eliminar', methods: ['POST'])]
    public function eliminar(int $id, PropiedadManager $propiedadManager): Response
    {
        try {
            $propiedadManager->eliminar($id);
            $this->addFlash('exito', 'Propiedad eliminada.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('propiedad_listar');
    }
}
