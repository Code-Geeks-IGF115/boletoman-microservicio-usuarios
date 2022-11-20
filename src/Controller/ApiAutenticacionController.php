<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use App\Repository\UsuarioRepository;

class ApiAutenticacionController extends AbstractController
{
    private ResponseHelper $responseHelper;
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper=$responseHelper;
    }

    #[Route('/api/registro', name: 'app_api_registro')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $params=$request->toArray(); 
        $plaintextPassword = $params["password"];
        $user = new Usuario();
        $user->setEmail($params["email"]);

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        try{
            $usuarioRepository->save($user, true);
        }catch(UniqueConstraintViolationException $e){
            return $this->responseHelper->responseDatos([
                'message' => 'Usuario ya existe'
            ]);
        }

        return $this->responseHelper->responseDatos([
            'message' => 'Usuario Registrado',
            'user' => $user,
        ]);
    }

    #[Route('/api/login', name: 'api_login')]
    public function login(Request $request): Response
    {
        $user=$this->getUser();
        if ($user==null) {
            return $this->json("Usuario o contraseña no válidos",Response::HTTP_UNAUTHORIZED);
        }
    
        $token ="Token Generado";

        return $this->json([
            'message' => 'Usuario Autenticado',
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
