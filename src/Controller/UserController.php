<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('dashboard/user',name: 'dashboard.user')]

class UserController extends AbstractController
{



    #[Route('/add', name: '.user.add')]
    public function addUser(ManagerRegistry $doctrine,Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $user = new User();
        $form= $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'user added');
            return $this->redirectToRoute('user');
        }else {
            return $this->render('user/add-user.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/edit/{id<\d+>?0}', name: '.user.edit')]
    public function editUser(ManagerRegistry $doctrine,Request $request,$id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);
        if(!$user){
            $user= new User();
        }
        $form= $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user');
        }else {
            return $this->render('user/add-user.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/{page<\d+>?1}/{nb<\d+>?12}', name: '.user')]
    public function userspage(ManagerRegistry $doctrine, $page, $nb): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $nbUsers = $repository->count([]);
        $nbPages = ceil($nbUsers / $nb);
        $Users = $repository->findBy([], [], $nb, ($page - 1) * $nb);
        return $this->render('user/index.html.twig', [
            'users' => $Users,
            'isPaginated' => true,
            'nbpages' => $nbPages,
            'page' => $page,
            'nb' => $nb
        ]);
    }



    #[Route('/delete/{id}', name: '.user.delete')]
    public function deleteUser(ManagerRegistry $doctrine,$id): RedirectResponse
    {
        $repository = $doctrine->getRepository(User::class);
        $user= $repository->find($id);
        if($user){
            $manager = $doctrine->getManager();
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'user deleted');
        }else{
            $this->addFlash('error', 'user not found');
        }
        return $this->redirectToRoute('user');

    }



}
