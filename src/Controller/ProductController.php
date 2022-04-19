<?php

namespace App\Controller;

class ProductController extends AbstractController
{
    /**
     * Display product page
     */
    public function index(): string
    {
        return $this->twig->render('Product/index.html.twig');
    }
}
