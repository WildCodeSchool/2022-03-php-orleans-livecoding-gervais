<?php

namespace App\Controller;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $user = $this->getUser();
        return $this->twig->render('Home/index.html.twig', [
            'user' => $user,
        ]);
    }
}
