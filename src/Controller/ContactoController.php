<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contacto;

final class ContactoController extends AbstractController
{
    #[Route('/contacto/{codigo?1}', name: 'contacto', requirements: ['codigo' => '[0-9]+'])]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);
        $html = "
        <h1>Ficha de contacto</h1>
        <p>Código: " . $contacto->getId() . "</p>
        <p>Nombre: " . $contacto->getNombre() . "</p>
        <p>Teléfono: " . $contacto->getTelefono() . "</p>
        <p>Email: " . $contacto->getEmail() . "</p>";
        return new Response($html);
    }
}
