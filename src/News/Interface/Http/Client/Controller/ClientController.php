<?php

namespace App\News\Interface\Http\Client\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('client/index.html.twig');
    }
}