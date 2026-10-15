<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        // On récupère le paramètre "?categorie=" dans l'URL (s'il y en a un)
        $category = $request->query->get('categorie');

        // On utilise notre nouvelle méthode au lieu de findAll() !
        $events = $eventRepository->findHomeEvents($category);

        return $this->render('home/index.html.twig', [
            'events' => $events,
            'currentCategory' => $category
        ]);
    }
}