<?php
// src/Controller/LegalController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    /**
     * @Route("/mentions-legales", name="legal_mentions_legales")
     */
    public function index(): Response
    {
        return $this->render('mentions_legales.html.twig');
    }
}
