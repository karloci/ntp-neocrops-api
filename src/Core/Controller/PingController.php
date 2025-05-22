<?php

namespace App\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PingController extends AbstractController
{
    #[Route('/ping', name: 'ping')]
    public function index(): JsonResponse
    {
        return $this->json('OK');
    }
}
