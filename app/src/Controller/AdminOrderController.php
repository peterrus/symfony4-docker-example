<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Order;

class AdminOrderController extends AbstractController
{
    /**
     * @Route("/admin/delete_all_orders", name="admin_delete_all_orders")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete_all_orders(EntityManagerInterface $entityManager)
    {
        $repository = $this->getDoctrine()
          ->getRepository(Order::class);

        foreach($repository->findAll() as $product)
        {
          $entityManager->remove($product);
        }

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Alle orders verwijderd'
        );

        return $this->redirectToRoute('welcome');
    }
}
