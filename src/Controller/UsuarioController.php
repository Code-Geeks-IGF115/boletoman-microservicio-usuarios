<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response,JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Nelmio\CorsBundle;

#[Route('/usuario')]
class UsuarioController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private $client;

    public function __construct(ResponseHelper $responseHelper, HttpClientInterface $client)
    {
        $this->responseHelper=$responseHelper;
        $this->client = $client;
    }

    #[Route('/', name: 'app_usuario_index', methods: ['GET'])]
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
    }

    #[Route('/{idUsuario}/misBoletos/compra/{idCompra}/boletos/pdf/new', name: 'app_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsuarioRepository $usuarioRepository,
    $idUsuario, $idCompra): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuarioRepository->save($usuario, true);

            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/boletos/pdf', name: 'ejemplo_usuario', methods: ['POST'])]
    public function ejemploUsuario(Request $request): JsonResponse
    {
        $mensaje="Hola Mundo!";
        
        try{
            // recibiendo parametros
            //SOY SERVIDOR
            $parametros=$request->toArray(); 
            $miNombre=$parametros["nombreCompleto"];
            // contruyendo cliente - AGREGACIÓN - TAMBIÉN SOY CLIENTE
            $response = $this->client->request(
                'POST', 
                'https://boletoman-reservaciones.herokuapp.com/sala/de/eventos/ejemplo/servidor', [
                // defining data using an array of parameters
                'json' => ['miNombre' => $miNombre],
            ]);
            $resultadosDeConsulta=$response->toArray();
            $mensaje=$resultadosDeConsulta["message"];
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($mensaje);  
        }

        return $this->responseHelper->responseMessage($mensaje);   


    }

    #[Route('/{id}', name: 'app_usuario_show', methods: ['GET'])]
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Usuario $usuario, UsuarioRepository $usuarioRepository): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuarioRepository->save($usuario, true);

            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $usuarioRepository->remove($usuario, true);
        }

        return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
    }
}
