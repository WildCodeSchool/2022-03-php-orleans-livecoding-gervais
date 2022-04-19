<?php

namespace App\Controller;

use App\Model\ProductManager;

class ProductController extends AbstractController
{
    /**
     * Display product page
     */
    public function index(): string
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll('name');

        return $this->twig->render('Product/index.html.twig', [
            'products' => $products,
        ]);
    }
}
