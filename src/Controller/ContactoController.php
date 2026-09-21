<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contacto;
use Symfony\Component\HttpFoundation\Request;

final class ContactoController extends AbstractController
{
    #[Route('/contacto/{codigo?1}', name: 'contacto', requirements: ['codigo' => '[0-9]+'])]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);
        return $this->render('ficha.html.twig', ['contacto' => $contacto]);
    }

    #[Route('/contacto/nuevo/{nombre}/{email}/{telefono}', name:'nuevo-con-datos')]
    public function NuevoconDatos(
        ManagerRegistry $doctrine,
        Request $request,
        string $nombre,
        string $email,
        string $telefono,
    ){
        $contacto = new Contacto();
        $contacto->setNombre($nombre);
        $contacto->setEmail($email);
        $contacto->setTelefono($telefono);
        
        $entityManager = $doctrine->getManager();
        $entityManager->persist($contacto);
        $entityManager->flush();
        
        return $this->redirectToRoute('contacto', ['codigo' => $contacto->getId()]);
    }

    #[Route('/contacto/empieza/{letra}', name: 'empieza-por')]
    public function empieza(ManagerRegistry $doctrine, Request $request, string $letra)
    {
    $repositorio = $doctrine->getRepository(Contacto::class);
    $contactos = $repositorio->startsWith($letra);
    return $this->render('lista_contactos.html.twig', [
        'contactos' => $contactos,
        'letra' => $letra,
    ]);
    }

    #[Route('/contacto/update/{codigo?1}/{nombre_nuevo}', name: 'update')]
    public function modificar(ManagerRegistry $doctrine, Request $request, int $codigo, string $nombre_nuevo){
        $contacto = $doctrine->getRepository(Contacto::class)->find($codigo);
        
        if($contacto){
            $contacto->setNombre($nombre_nuevo);
            $entityManager = $doctrine->getManager();
            try{
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('contacto', ['codigo' => $contacto->getId()]);
            
            }catch (\Exception $e){
                error_log('Error al actualizar el contacto: ' . $e->getMessage());
                return new Response ("Error insertando objeto".$e->getMessage());
            }
        }
        return $this->redirectToRoute('contacto', ["codigo" => null]);

    }

    #[Route('/contacto/borrar/{codigo}', name:'borrar')]
    public function borrar(ManagerRegistry $doctrine, int $codigo){
        $contacto = $doctrine->getRepository(Contacto::class)->find($codigo);
        
        if ($contacto){
            $entityManager = $doctrine->getManager();

            try{
                $entityManager->remove($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('inicio');

            }catch(\Exception $e){
                error_log('Error al eliminar el contacto: ' . $e->getMessage());
                return new Response ("Error eliminando objeto".$e->getMessage());
            }
            
        } else{
            return new Response("No se ha encontrado el contacto");
        }
    }


}