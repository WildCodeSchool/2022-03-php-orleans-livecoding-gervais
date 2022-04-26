<?php

namespace App\Controller;

use App\Model\UserManager;

class LoginController extends AbstractController
{
    public function login(): string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credentials = array_map('trim', $_POST);
            if (empty($credentials['email'])) {
                $errors[] = 'Le champ email ne doit pas être vide';
            }
            if (empty($credentials['password'])) {
                $errors[] = 'Le champ mot de passe ne doit pas être vide';
            }
            if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format d\'email invalide';
            }

            if (empty($errors)) {
                $userManager = new UserManager();
                $user = $userManager->selectOneByEmail($credentials['email']);
                if ($user) {
                    if (password_verify($credentials['password'], $user['password'])) {
                        $_SESSION['user'] = $user['id'];
                        header('Location: /');
                    } else {
                        $errors[] = 'Mauvais identifiants';
                    }
                } else {
                    $errors[] = 'Email inconnu';
                }
            }
        }
        return $this->twig->render('Login/login.html.twig', [
            'errors' => $errors,
        ]);
    }

    public function logout()
    {
        if (!empty($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        header('Location: /');
    }
}
