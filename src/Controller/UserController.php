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

#[Route('user')]

class UserController extends AbstractController
{

//    #[Route('/add', name: 'user.add')]
//    public function addUser(ManagerRegistry $doctrine): Response
//    {
//        $entityManager = $doctrine->getManager();
//        $user = new User();
//        $user->setFullName('John Doe');
//        $user->setUserName('johndoe');
//        $user->setEmail('gjjvjj');
//        $user->setPhone('jnjkn');
//        $user->setPassword('jnjkn');
//
//
//        $entityManager->persist($user);
//
//        $entityManager->flush();
//
//
//        return $this->redirectToRoute('user');
//    }

    #[Route('/add', name: 'user.add')]
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

    #[Route('/edit/{id<\d+>?0}', name: 'user.edit')]
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

    #[Route('/{page<\d+>?1}/{nb<\d+>?12}', name: 'user.findby.pages')]
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

    #[Route('/', name: 'user')]
    public function index( ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(User::class);
        $users=$repository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/{id<\d+>}', name: 'find.by')]
    public function findby( ManagerRegistry $doctrine ,$id): Response
    {
        $repository= $doctrine->getRepository(User::class);
        $user=$repository->find($id);
        if(!$user){
            return $this->render('user/notfound.html.twig',[
                'id'=>$id,
            ]);
        }
        return $this->render('user/detail.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'user.delete')]
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

    #[Route('/update/{id}/{fullname}/{username}/{email}/{phone}/{password}', name: 'user.update')]
    public function updateUser($id, ManagerRegistry $doctrine, $fullname, $username, $email, $phone, $password): RedirectResponse {
        $repository = $doctrine->getRepository(User::class);
        $user= $repository->find($id);
        if ($user) {
            // Si la personne existe => mettre a jour notre personne + message de succes
            $user->setFullName($fullname);
            $user->setUserName($username);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setPassword($password);
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "La personne a été mis à jour avec succès");
        }  else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne innexistante");
        }
        return $this->redirectToRoute('user.findby.pages');
    }


}
