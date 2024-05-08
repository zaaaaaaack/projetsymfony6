<?php

namespace App\Controller;


use phpDocumentor\Reflection\Types\Boolean;
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
        if($this->redirectAdmin()){
            return $this->redirectToRoute("dashboard.product.stats");
        }
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
        if($this->redirectAdmin()){
            return $this->redirectToRoute("dashboard.product.stats");
        }
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
            $this->addFlash("success","Ce produit est ajouté à votre chariot avec sucées");
        else {
            return $this->redirectToRoute('products_base');
        }
        return $this->render('cart/index.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/base/contact', name: 'contact')]
    public function contact(): Response
    {
        if($this->redirectAdmin()){
            return $this->redirectToRoute("dashboard.product.stats");
        }
        return $this->forward("App\Controller\FormulaireController::addForm");
    }
    private function redirectAdmin():Bool
    {
        if($this->getUser() && in_array("ROLE_ADMIN",$this->getUser()->getRoles())){
            return true;
        }
        return false;

    }

}




