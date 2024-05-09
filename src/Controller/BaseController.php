<?php

namespace App\Controller;

use App\Form\ProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class BaseController extends AbstractController
{


    #[Route('/shop', name: 'products_base')]
    public function index(ProductRepository $productRepository): Response
    {
        $menProducts = $productRepository->findByCategory('Men sportswear');
        $womenProducts = $productRepository->findByCategory('Women sportswear');
        $supplements = $productRepository->findByCategory('Supplements');

        return $this->render('base/index.html.twig', [
            'menProducts' => $menProducts,
            'womenProducts' => $womenProducts,
            'supplements' => $supplements,
        ]);
    }

    #[Route('/products/{id}', name: 'product_details')]
    public function show($id, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductFormType::class);
        $form->handleRequest($request);



        return $this->render('base/product_details.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/base/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->forward("App\Controller\FormulaireController::addForm");
    }
}




