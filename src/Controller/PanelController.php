<?php

namespace App\Controller;

use App\Manager\PanelManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanelController extends AbstractController
{
    #[Route('/', name: 'app_panel')]
    public function index(PanelManager $panelManager): Response
    {
        $resumen = $panelManager->getResumen();

        return $this->render('panel/index.html.twig', [
            'resumen' => $resumen,
        ]);
    }
}
