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

#[Route('formulaire')]

class FormulaireController extends AbstractController
{

//    #[Route('/add', name: 'form.add')]
//    public function addForm(ManagerRegistry $doctrine,Request $request): Response
//    {
//        $entityManager = $doctrine->getManager();
//        $form = new FormFixture();
//        $form= $this->createForm(UserType::class, $form);
//        $form->handleRequest($request);
//        if($form->isSubmitted() && $form->isValid()){
//            $entityManager->persist($user);
//            $entityManager->flush();
//            $this->addFlash('success', 'user added');
//            return $this->redirectToRoute('user');
//        }else {
//            return $this->render('form/add-user.html.twig', [
//                'form' => $form->createView(),
//            ]);
//        }
//    }

      #[Route('/add', name: 'form.add')]
      public function addForm( ManagerRegistry $doctrine ): Response
      {
          $entityManager = $doctrine->getManager();
          $form = new Formulaire();
          $form->setUserName('John Doe');
          $form->setEmail('hkhk');
          $form->setMessage("njknv");
            $entityManager->persist($form);
            $entityManager->flush();
            return $this->redirectToRoute('form.page');
        }

    #[Route('/edit/{id<\d+>?0}', name: 'form.edit')]
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

    #[Route('/{page<\d+>?1}/{nb<\d+>?10}', name: 'form.page')]
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

    #[Route('/', name: 'formulaire')]
    public function index( ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Formulaire::class);
        $forms=$repository->findAll();
        return $this->render('formulaire/index.html.twig', [
            'forms' => $forms,
        ]);
    }


    #[Route('/{id<\d+>}', name: 'find.by')]
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

    #[Route('/delete/{id}', name: 'form.delete')]
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

//    #[Route('/update/{id}/{fullname}/{username}/{email}/{phone}/{password}', name: 'user.update')]
//    public function updateUser($id, ManagerRegistry $doctrine, $fullname, $username, $email, $phone, $password): RedirectResponse {
//        $repository = $doctrine->getRepository(User::class);
//        $user= $repository->find($id);
//        if ($user) {
//            // Si la personne existe => mettre a jour notre personne + message de succes
//            $user->setFullName($fullname);
//            $user->setUserName($username);
//            $user->setEmail($email);
//            $user->setPhone($phone);
//            $user->setPassword($password);
//            $manager = $doctrine->getManager();
//            $manager->persist($user);
//            $manager->flush();
//            $this->addFlash('success', "La personne a été mis à jour avec succès");
//        }  else {
//            //Sinon  retourner un flashMessage d'erreur
//            $this->addFlash('error', "Personne innexistante");
//        }
//        return $this->redirectToRoute('user.findby.pages');
//    }


}
