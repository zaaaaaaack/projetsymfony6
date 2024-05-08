<?php

namespace App\Controller;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name:'cart_')]
class CartController extends AbstractController
{
  #[Route('/',name:'index')]
   public function index(SessionInterface $session,EntityManagerInterface $entityManager){
    $panier=$session->get('panier',[]);
    $data=[];
    $total=0;
    foreach ($panier as $id =>$quantity) {
        $product = $entityManager->getRepository(Product::class)->find($id);
       if($product)
        {
            $data[$id]=['product'=>$product,'quantity'=>$quantity];
            $total += $product->getPrice()*$quantity ;
        }else{
           unset($panier[$id]);
        }
        
       
    }
    return $this->render('cart/index.html.twig',['data'=>$data,'subtotal'=>$total]);
   }
   
  #[Route('/add/{id}',name:'add')]
  public function add(int $id,int $quantity, SessionInterface $session, EntityManagerInterface $entityManager){
    $product = $entityManager->getRepository(Product::class)->find($id);
    $panier=$session->get('panier',[]);
    if (empty($panier[$id])){
        $panier[$id]= $quantity;
    }else{
        $panier[$id]+=$quantity;
    }
    $session->set('panier', $panier);
    return $this->redirectToRoute('cart_index');
}


#[Route('/update',name:'update')]
    public function update(Request $request,SessionInterface $session): Response
    {
        $productId = $request->request->get('product_id');
        $action = $request->request->get('action');
        $panier=$session->get('panier',[]);
        if ($action === 'add') {
            return $this->redirectToRoute('add', ['id' => $productId ,'quantity'=>1]);

        }elseif ($action === 'subtract') {
            if(!empty($panier[$productId])){
                if($panier[$productId]>1){
                    $panier[$productId]--;
                }
                else{
                    unset($panier[$productId]);
                }
            }
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('cart_index');
}



#[Route('/delete/{id}',name:'delete')]
public function delete(int $id, SessionInterface $session, ){
    $panier=$session->get('panier',[]);
    if(!empty($panier[$id])){
            unset($panier[$id]);
        }
    $session->set('panier', $panier);
    return $this->redirectToRoute('cart_index');
}


#[Route('/empty',name:'empty')]
public function empty(int $id, SessionInterface $session, ){
    $session->remove('panier');
    return $this->redirectToRoute('cart_index');
}

#[Route('/placeorder',name:'placeorder')]
public function placeorder(int $id, SessionInterface $session, ){
    $session->remove('panier');
    return $this->render('cart/placeorder.html.twig');

}

}
  
  


