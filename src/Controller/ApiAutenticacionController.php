<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ResponseHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        $resultado=$usuarioRepository->save($user, true);

        return $this->json([
            'message' => 'Usuario Registrado',
            'user' => $resultado,
        ]);
    }
}
