<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use App\Repository\UsuarioRepository;
use App\Entity\Usuario;
use Symfony\Component\Validator\Constraints\Regex;

class ApiAutenticacionController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private JWTTokenManagerInterface $JWTManager;
    public function __construct(ResponseHelper $responseHelper, JWTTokenManagerInterface $JWTManager)
    {
        $this->responseHelper=$responseHelper;
        $this->JWTManager=$JWTManager;
    }

    #[Route('/api/registro', name: 'app_api_registro')]
    public function registro(
        Request $request,
    UserPasswordHasherInterface $passwordHasher, 
    UsuarioRepository $usuarioRepository,
    ): JsonResponse
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
            'token' => $this->getTokenUser($user),
        ]);
    }
    public function getTokenUser(UserInterface $user)
    {
        return $this->JWTManager->create($user);
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
            'token' => $token]);
    }
    #[Route('/api/login_check', name: 'api_login_check')]
    public function login_check(){}
    #[Route('/api/test', name: 'api_test')]
    public function test(Request $request): JsonResponse
    {
        $user=$this->getUser();
        if ($user==null) {
            return $this->json("Usuario o contraseña no válidos",Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message' => 'TEst',
            'user'  => $user->getUserIdentifier(),
        ]);
    }
}
