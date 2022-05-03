<?php

namespace App\Controller;

class ContactController extends AbstractController
{
    /**
     * Display contact page
     */
    public function index(): string
    {
        return $this->twig->render('Contact/index.html.twig', [
        ]);
    }
}
