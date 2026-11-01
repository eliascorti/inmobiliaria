<?php

namespace App\Manager;

use App\Entity\Contrato;
use App\Entity\Propiedad;
use App\Repository\ClienteRepository;
use App\Repository\ContratoRepository;
use App\Repository\PropiedadRepository;

class PanelManager
{
    public function __construct(
        private ClienteRepository $clienteRepository,
        private PropiedadRepository $propiedadRepository,
        private ContratoRepository $contratoRepository,
    ) {
    }

    // Indicadores del panel principal (RF-03)
    public function getResumen(): array
    {
        return [
            'clientes' => $this->clienteRepository->count([]),
            'disponibles' => $this->propiedadRepository->count(['estado' => Propiedad::DISPONIBLE]),
            'vigentes' => $this->contratoRepository->count(['estado' => Contrato::VIGENTE]),
        ];
    }
}
