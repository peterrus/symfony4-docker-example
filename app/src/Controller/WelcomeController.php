<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

use App\Entity\Product;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     */
    public function index(Security $security)
    {
      if($security->isGranted('ROLE_ADMIN'))
      {
        return $this->redirectToRoute('admin_product_list');
      }

      $products = $this->getDoctrine()
        ->getRepository(Product::class)
        ->findAll();

        return $this->render('welcome/index.html.twig', [
            'products' => $products,
        ]);
    }
}
