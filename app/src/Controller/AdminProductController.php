<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use App\Entity\Product;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="admin_product_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $products = $this->getDoctrine()
          ->getRepository(Product::class)
          ->findAll();

        return $this->render('admin/product_list.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/admin/product/create", name="admin_product_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request)
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
          ->add('name', TextType::class, ['label' => 'Naam'])
          ->add('price', MoneyType::class, ['label' => 'Naam'])
          ->add('save', SubmitType::class, ['label' => 'Product aanmaken'])
          ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Nieuw product is opgeslagen'
            );

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render('admin/product_create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
