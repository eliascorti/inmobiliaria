<?php

namespace App\Manager;

use App\Entity\Contrato;
use App\Entity\Propiedad;
use App\Entity\Solicitud;
use App\Repository\ClienteRepository;
use App\Repository\ContratoRepository;
use App\Repository\PropiedadRepository;
use App\Repository\SolicitudRepository;

class PanelManager
{
    public function __construct(
        private ClienteRepository $clienteRepository,
        private PropiedadRepository $propiedadRepository,
        private ContratoRepository $contratoRepository,
        private SolicitudRepository $solicitudRepository,
    ) {
    }

    // Indicadores del panel principal (RF-03)
    public function getResumen(): array
    {
        return [
            'clientes' => $this->clienteRepository->count([]),
            'disponibles' => $this->propiedadRepository->count(['estado' => Propiedad::DISPONIBLE]),
            'vigentes' => $this->contratoRepository->count(['estado' => Contrato::VIGENTE]),
            'pendientes' => $this->solicitudRepository->count(['estado' => Solicitud::PENDIENTE]),
        ];
    }
}
