<?php

namespace App\Controller;

use App\Model\ProductManager;

class AdminProductController extends AbstractController
{
    public const MAX_LENGTH = 255;
    public const AUTHORIZED_MIMES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    public const MAX_FILE_SIZE = 1000000;

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
            $productErrors = $this->validateProduct($product);
            $imageFile = $_FILES['image'];
            $imageErrors = $this->validateImage($imageFile);

            $errors = [...$productErrors, ...$imageErrors];

            /** @phpstan-ignore-next-line */
            if (empty($errors)) {
                // if validation is ok, insert and redirection

                $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('', true) . '.' . $extension;

                move_uploaded_file($imageFile['tmp_name'], UPLOAD_PATH . '/' . $imageName);

                $productManager = new ProductManager();
                $product['image'] = $imageName;
                $productManager->insert($product);

                header('Location:/admin/produits');
                return null;
            }
        }

        return $this->twig->render('Admin/Product/add.html.twig', ['errors' => $errors]);
    }

    private function validateProduct(array $product): array
    {
        $errors = [];
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

        return $errors;
    }

    private function validateImage(array $files): array
    {
        $errors = [];
        if ($files['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Le fichier est obligatoire';
        } elseif ($files['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Problème de téléchargement du fichier';
        } else {
            if ($files['size'] > self::MAX_FILE_SIZE) {
                $errors[] = 'Le fichier doit faire moins de ' . self::MAX_FILE_SIZE / 1000000 . 'Mo';
            }

            if (!in_array(mime_content_type($files['tmp_name']), self::AUTHORIZED_MIMES)) {
                $errors[] = 'Le fichier doit être de type ' . implode(', ', self::AUTHORIZED_MIMES);
            }
        }

        return $errors;
    }
}
