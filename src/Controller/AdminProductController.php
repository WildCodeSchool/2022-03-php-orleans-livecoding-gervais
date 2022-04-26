<?php

namespace App\Controller;

use App\Model\ProductManager;

class AdminProductController extends AbstractController
{
    /**
     * Display product page
     */
    public function index(): string
    {
        if ($this->getUser() === null) {
            echo 'Pas autorisé';
            header('HTTP/1.0 403 Forbidden');
            return '';
        }
        $productManager = new ProductManager();
        $products = $productManager->selectAll('name');

        return $this->twig->render('Admin/Product/index.html.twig', [
            'products' => $products,
        ]);
    }
}
