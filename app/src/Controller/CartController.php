<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;

use StdClass;
use Memcached;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(UserInterface $user)
    {
        $user_id = $user->getId();
        $items = [];

        $memcache = new Memcached();
        $memcache->addServer('memcached', 11211);

        $current_cart_raw = $memcache->get('cart-' . $user_id);
        if ($current_cart_raw) {
            // previous cart exists
            $current_cart = unserialize($current_cart_raw);

            foreach ($current_cart->getItems() as $current_cart_item_productid => $current_cart_item_amount) {

                $product = $this->getDoctrine()
                  ->getRepository(Product::class)
                  ->find($current_cart_item_productid);

                // create viewmodel using a stdclass (useful for one-off viewmodels)
                $item_model = new CartItem($product, $current_cart_item_amount);

                array_push($items, $item_model);
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id, UserInterface $user)
    {
        $user_id = $user->getId();

        $product = $this->getDoctrine()
          ->getRepository(Product::class)
          ->find($id);

        $memcache = new Memcached();
        $memcache->addServer('memcached', 11211);

        $current_cart_raw = $memcache->get('cart-' . $user_id);

        // check if a cart already exists in memcache
        if ($current_cart_raw) {
            // deserialize cart
            $current_cart = unserialize($current_cart_raw);
        } else {
            // create new cart
            $current_cart = new Cart($user_id);
        }

        $current_cart->addItem($product->getId());

        $memcache->set('cart-' . $user_id, serialize($current_cart));

        $this->addFlash(
            'success',
            'Product toegevoegd aan winkelwagen'
        );

        return $this->redirectToRoute('welcome');
    }

    /**
     * @Route("/cart/pay", name="cart_pay")
     */
    public function pay(EntityManagerInterface $entityManager, \Swift_Mailer $mailer, UserInterface $user)
    {
        $user_id = $user->getId();
        $items = [];

        $memcache = new Memcached();
        $memcache->addServer('memcached', 11211);

        $current_cart_raw = $memcache->get('cart-' . $user_id);
        if ($current_cart_raw) {
            // previous cart exists
            $current_cart = unserialize($current_cart_raw);

            // create new order
            $order = new Order();

            foreach ($current_cart->getItems() as $current_cart_item_productid => $current_cart_item_amount) {
                $product = $this->getDoctrine()
                  ->getRepository(Product::class)
                  ->find($current_cart_item_productid);

                $order_item = new OrderItem();
                $order_item->setProduct($product);
                $order_item->setAmount($current_cart_item_amount);

                $order->addItem($order_item);
                $entityManager->persist($order_item);
            }

            $entityManager->persist($order);

            $entityManager->flush();

            $memcache->delete('cart-' . $user_id);

            $message = (new \Swift_Message('Bedankt voor uw bestelling'))
                ->setFrom('info@awesomeshop.com')
                ->setTo('user@email.nl')
                ->setBody(
                    $this->renderView(
                        'email/order_done.html.twig',
                        ['order' => $order]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash(
              'success',
              'Bedankt voor uw bestelling!'
            );
        } else {
            $this->addFlash(
              'error',
              'Winkelmand is leeg'
          );
        }

        return $this->redirectToRoute('welcome');
    }
}

//viewmodels
class CartItem
{
    public $product;
    public $amount;

    public function __construct($product, $amount)
    {
        $this->product = $product;
        $this->amount = $amount;
    }
}
