<?php

namespace App\Controller;

use App\Model\ProductManager;

class AdminProductController extends AbstractController
{
    public const MAX_LENGTH = 255;
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

    /**
     * Add a new product
     */
    public function add(): ?string
    {
        if ($this->getUser() === null) {
            echo 'Pas autorisé';
            header('HTTP/1.0 403 Forbidden');
            return '';
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $product = array_map('trim', $_POST);

            // TODO validations (length, format...)
            if (empty($product['name'])) {
                $errors[] = 'Le champ nom est obligatoire';
            }
            if (empty($product['price'])) {
                $errors[] = 'Le champ prix est obligatoire';
            }
            if (!is_numeric($product['price']) || $product['price'] < 0) {
                $errors[] = 'Le champ prix doit être un nombre positif';
            }

            if (strlen($product['name']) > self::MAX_LENGTH) {
                $errors[] = 'Le champ nom doit faire moins de ' . self::MAX_LENGTH . ' caractères';
            }

            if (empty($errors)) {
                // if validation is ok, insert and redirection
                $productManager = new ProductManager();
                $productManager->insert($product);

                header('Location:/admin/produits');
                return null;
            }
        }

        return $this->twig->render('Admin/Product/add.html.twig', ['errors' => $errors]);
    }
}
