<?php

namespace App\service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $sessionInterface;
    public function __construct(SessionInterface $sessionInterface)
    {
        $this->sessionInterface=$sessionInterface;
    }
    public function addtoCart(int $id)
    {
        $card =$this->sessionInterface->get("cart",[]);
        $this->sessionInterface->set("cart",$card);
    }

}