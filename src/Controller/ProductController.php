<?php

namespace App\Controller;

use App\Entity\Formulaire;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('dashboard/product',name: "dashboard.product")]

class ProductController extends AbstractController
{



    #[Route('/add', name: '.product.add')]
    public function addProduct(ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger): Response
    {
        $entityManager = $doctrine->getManager();
        $product = new Product();
        $form= $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'clothes/'.$safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('product_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImg($newFilename);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'product added');
            return $this->redirectToRoute('dashboard.product.stats');
        }else {
            return $this->render('product/add-product.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/edit/{id<\d+>?0}', name: '.product.edit')]
    public function editProduct(ManagerRegistry $doctrine,Request $request,$id,SluggerInterface $slugger): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Product::class);
        $product = $repository->find($id);
        if(!$product){
            $product= new Product();
        }
        $form= $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'clothes/'.$safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('product_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImg($newFilename);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('dashboard.product.stats');
        }else {
            return $this->render('product/add-product.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/{page<\d+>?1}/{nb<\d+>?12}', name: '.product')]
    public function userspage(ManagerRegistry $doctrine, $page, $nb): Response
    {
        $repository = $doctrine->getRepository(Product::class);
        $nbPr = $repository->count([]);
        $nbPages = ceil($nbPr / $nb);
        $Products = $repository->findBy([], [], $nb, ($page - 1) * $nb);
        return $this->render('product/index.html.twig', [
            'products' => $Products,
            'isPaginated' => true,
            'nbpages' => $nbPages,
            'page' => $page,
            'nb' => $nb
        ]);
    }




    #[Route('/{id<\d+>}', name: '.find.by')]
    public function findby( ManagerRegistry $doctrine ,$id): Response
    {
        $repository= $doctrine->getRepository(Product::class);
        $product=$repository->find($id);
        if(!$product){
            return $this->render('product/notfound.html.twig',[
                'id'=>$id,
            ]);
        }
        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/delete/{id}', name: '.product.delete')]
    public function deleteProduct(ManagerRegistry $doctrine,$id): RedirectResponse
    {
        $repository = $doctrine->getRepository(Product::class);
        $product= $repository->find($id);
        if($product){
            $manager = $doctrine->getManager();
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('success', 'product deleted');
        }else{
            $this->addFlash('error', 'product not found');
        }
        return $this->redirectToRoute('dashboard.product.stats');

    }



    #[Route('/stats', name: '.stats')]
    public function stats(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Product::class);
        $nbPr = $repository->count([]);
        $repository = $doctrine->getRepository(User::class);
        $nbUs = $repository->count([]);
        $repository = $doctrine->getRepository(Formulaire::class);
        $nbFo = $repository->count([]);
        $repository = $doctrine->getRepository(Product::class);
        $cm= $repository->countProductsByCategory("Men sportswear");
        $cw= $repository->countProductsByCategory("Women sportswear");
        $cs= $repository->countProductsByCategory("Supplements");

        return $this->render('product/stats.html.twig',[
            'nbPr' => $nbPr,
            'nbUs' => $nbUs,
            'nbFo' => $nbFo,
            'cm' => $cm,
            'cw' => $cw,
            'cs' => $cs,
        ]);

    }


}
