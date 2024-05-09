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
    public function index(SessionInterface $session, EntityManagerInterface $entityManager,Request $request): Response
    {
        $panier = $session->get('panier',[]);

        $data = [];
        $subtotal = 0;

        // Retrieve all product IDs from the cart
        $productIds = array_keys($panier);

        // Retrieve all products from the database using one query
        $products = $entityManager->getRepository(Product::class)->findBy(['id' => $productIds]);

        foreach ($products as $product) {
            $id = $product->getId();
            $quantity = $panier[$id] ?? 0;

            // Calculate subtotal
            $subtotal += $product->getPrice() * $quantity;

            // Store product data
            $data[$id] = ['product' => $product, 'quantity' => $quantity];
        }

        // Update session with current cart data
        $session->set('panier', $panier);

        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'subtotal' => $subtotal
        ]);
    }

  #[Route('/add/{id}',name:'add')]
  public function add(int $id, SessionInterface $session, EntityManagerInterface $entityManager,Request $request){
        $quantity =$request->get('product_form')['quantity'];

        $panier=$session->get('panier',[]);
        if (isset($panier[$id])){
            $panier[$id]+= $quantity;
        }else{
            $panier[$id]=$quantity;
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
            $panier[$productId]++;


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
public function empty(SessionInterface $session, ){
    $session->remove('panier');
    return $this->redirectToRoute('cart_index');
}

    #[Route('/placeorder',name:'placeorder')]
    public function placeorder( SessionInterface $session, ){
        if ( $this->getUser() && in_array("ROLE_ADMIN",$this->getUser()->getRoles())) {
            return $this->redirectToRoute('dashboard.product.stats');
        }
        else{
            if(($this->getUser())==null){
                return $this->redirectToRoute('app_login');

            }
        }
        $session->remove('panier');
        return $this->render('cart/placeorder.html.twig');

    }

}
  
  


