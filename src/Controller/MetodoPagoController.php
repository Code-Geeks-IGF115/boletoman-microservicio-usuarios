<?php

namespace App\Controller;

use App\Entity\MetodoPago;
use App\Form\MetodoPagoType;
use App\Repository\MetodoPagoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\ResponseHelper;
use Symfony\Component\HttpFoundation\JsonResponse;




#[Route('/metodo/pago')]
class MetodoPagoController extends AbstractController
{
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper=$responseHelper;
    }



    #[Route('/', name: 'app_metodo_pago_index', methods: ['GET'])]
    public function index(MetodoPagoRepository $metodoPagoRepository): Response
    {
        return $this->render('metodo_pago/index.html.twig', [
            'metodo_pagos' => $metodoPagoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_metodo_pago_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MetodoPagoRepository $metodoPagoRepository): Response
    {
        $metodoPago = new MetodoPago();
        $form = $this->createForm(MetodoPagoType::class, $metodoPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $metodoPagoRepository->save($metodoPago, true);

            return $this->responseHelper->responseMessage("Metodo de pago guardado");

        }else{

            return $this->responseHelper->responseMessage($form->getErrors());
        

        }

        
        
    }

    #[Route('/{id}', name: 'app_metodo_pago_show', methods: ['GET'])]
    public function show(MetodoPago $metodoPago): Response
    {
        return $this->render('metodo_pago/show.html.twig', [
            'metodo_pago' => $metodoPago,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_metodo_pago_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MetodoPago $metodoPago, MetodoPagoRepository $metodoPagoRepository): Response
    {
        $form = $this->createForm(MetodoPagoType::class, $metodoPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $metodoPagoRepository->save($metodoPago, true);

            return $this->redirectToRoute('app_metodo_pago_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('metodo_pago/edit.html.twig', [
            'metodo_pago' => $metodoPago,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_metodo_pago_delete', methods: ['POST'])]
    public function delete(Request $request, MetodoPago $metodoPago, MetodoPagoRepository $metodoPagoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$metodoPago->getId(), $request->request->get('_token'))) {
            $metodoPagoRepository->remove($metodoPago, true);
        }

        return $this->redirectToRoute('app_metodo_pago_index', [], Response::HTTP_SEE_OTHER);
    }
}
