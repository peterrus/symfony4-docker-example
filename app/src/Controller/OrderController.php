<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;

class OrderController extends AbstractController
{
    /**
     * @Route("/orders", name="order_list")
     */
    public function index()
    {
        $orders = $this->getDoctrine()
          ->getRepository(Order::class)
          ->findAll();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order/{id}", name="order_show")
     */
    public function show($id)
    {
        $order = $this->getDoctrine()
          ->getRepository(Order::class)
          ->find($id);

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
