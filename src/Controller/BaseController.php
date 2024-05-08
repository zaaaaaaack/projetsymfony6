<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class BaseController extends AbstractController
{

    #[Route('/shop', name: 'app_base')]
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
    #[Route('/product/{id}', name: 'product_details')]
    public function show($id, ProductRepository $productRepository): Response
    {
        if($this->redirectAdmin()){
            return $this->redirectToRoute("dashboard.product.stats");
        }
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

       return $this->render('base/product_details.html.twig', [
            'product' => $product,
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

