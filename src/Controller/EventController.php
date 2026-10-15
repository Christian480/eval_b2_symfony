<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class EventController extends AbstractController
{
    #[Route('/evenement/ajouter', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        // L'événement n'est pas validé par défaut
        $event->setIsValidated(false);

        // Création du formulaire
        $form = $this->createFormBuilder($event)
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('date', DateType::class, ['widget' => 'single_text', 'label' => 'Date'])
            ->add('informations', TextType::class, ['label' => 'Informations'])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices'  => [
                    'Concert' => 'Concert',
                    'Conférence' => 'Conférence',
                    'Atelier' => 'Atelier',
                    'Sport' => 'Sport',
                ],
            ])
            ->add('seats', IntegerType::class, ['label' => 'Nombre de places'])
            ->add('save', SubmitType::class, ['label' => 'Créer l\'événement'])
            ->getForm();

        $form->handleRequest($request);

        // Traitement du formulaire à la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        // On envoie le formulaire à la vue
        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
    public function show(): Response
    {
        return $this->render('event/show.html.twig');
    }
}