<?php

namespace App\Controller;

use App\Entity\{Usuario};
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

    #[Route('/new', name: 'app_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuarioRepository->save($usuario, true);

           //return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        $usuario = new Usuario();
        $usuario->setUserr("usuario3");
        $usuario->setPassword("789");
        $usuarioRepository->save($usuario, true);
        return $this->responseHelper->responseDatos($usuario);
    }

    #[Route('/{idUsuario}', name: 'boletos', methods: ['POST'])]
    public function boletos($idUsuario): JsonResponse
    {
        $mensaje="Hola Mundo!";
        
        try{
            // recibiendo parametros
            //SOY SERVIDOR
            //$parametros=$request->toArray(); 
            //$miCompra=$parametros["idCompra"];
            // contruyendo cliente - AGREGACIÓN - TAMBIÉN SOY CLIENTE
            $response = $this->client->request(
                'POST', 
                'https://boletoman-compras.herokuapp.com/compra/'. $idUsuario .'/boletos/pdf'
                
                // defining data using an array of parameters
                //'json' => ['miNombre' => "HOLA"],
            );
            $resultadosDeConsulta=$response->toArray();
            $mensaje=$resultadosDeConsulta;
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($e);  
        }
        //dd($resultadosDeConsulta);
        return $this->responseHelper->responseDatos($mensaje);   


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
