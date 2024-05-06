<?php

namespace App\Controller;

use App\DataFixtures\FormFixture;
use App\Entity\Formulaire;
use App\Form\FormulaireType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('dashboard/formulaire',name: "dashboard.formulaire")]

class FormulaireController extends AbstractController
{

    #[Route('/add', name: '.form.add')]
    public function addForm(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        // Create a new instance of the Formulaire entity
        $formulaire = new Formulaire();

        // Create the form and handle the request
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the data from the form
            $entityManager->persist($formulaire);
            $entityManager->flush();

            // Add a flash message and redirect
            $this->addFlash('success', 'Form added');
            return $this->redirectToRoute('app_base');
        }

        // Render the form template
        return $this->render('formulaire/contact.html.twig', [
            'form' => $form->createView(), // Pass 'form' variable to the template
        ]);
    }




    #[Route('/edit/{id<\d+>?0}', name: '.form.edit')]
    public function editForm(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Formulaire::class);
        $formEntity = $repository->find($id);

        if (!$formEntity) {
            $formEntity = new Formulaire();
        }

        $form = $this->createForm(FormulaireType::class, $formEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the entity instance corresponding to the form data
            $entityManager->persist($formEntity);
            $entityManager->flush();

            return $this->redirectToRoute('form.page');
        } else {
            return $this->render('formulaire/add-form.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/{page<\d+>?1}/{nb<\d+>?10}', name: '.formulaire')]
    public function formpage(ManagerRegistry $doctrine, $page, $nb): Response
    {
        $repository = $doctrine->getRepository(Formulaire::class);
        $nbform = $repository->count([]);
        $nbPages = ceil($nbform / $nb);
        $forms = $repository->findBy([], [], $nb, ($page - 1) * $nb);
        return $this->render('formulaire/index.html.twig', [
            'forms' => $forms,
            'isPaginated' => true,
            'nbpages' => $nbPages,
            'page' => $page,
            'nb' => $nb
        ]);
    }


    #[Route('/{id<\d+>}', name: '.find.by')]
    public function findby( ManagerRegistry $doctrine ,$id): Response
    {
        $repository= $doctrine->getRepository(Formulaire::class);
        $form=$repository->find($id);
        if(!$form){
            return $this->render('formulaire/notfound.html.twig',[
                'id'=>$id,
            ]);
        }
        return $this->render('formulaire/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: '.form.delete')]
    public function deleteForm(ManagerRegistry $doctrine,$id): RedirectResponse
    {
        $repository = $doctrine->getRepository(Formulaire::class);
        $form= $repository->find($id);
        if($form){
            $manager = $doctrine->getManager();
            $manager->remove($form);
            $manager->flush();
            $this->addFlash('success', 'form deleted');
        }else{
            $this->addFlash('error', 'form not found');
        }
        return $this->redirectToRoute('form.page');

    }




}
