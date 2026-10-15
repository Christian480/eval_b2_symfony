<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class EventController extends AbstractController
{
    #[Route('/evenement/ajouter', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        // L'événement a le statut "Non validé" par défaut
        $event->setIsValidated(false);

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
            ->add('seats', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => ['min' => 1]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image à la une (JPG, PNG ou WebP)',
                'mapped' => false, 
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WebP)',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer l\'événement'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion de l'erreur
                }

                $event->setImage($newFilename);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
   
    public function show(int $id, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Cet événement n\'existe pas.');
        }

        // Si le bouton S'inscrire / Se désinscrire est cliqué (requête POST)
        if ($request->isMethod('POST')) {
            $user = $this->getUser(); // On récupère l'utilisateur connecté

            if ($user) {
                // Si l'utilisateur est déjà dans la liste des participants, on le retire (désinscription)
                if ($event->getParticipants()->contains($user)) {
                    $event->removeParticipant($user);
                } else {
                    // Sinon, on l'ajoute (inscription)
                    $event->addParticipant($user);
                }

                // On sauvegarde les changements dans la base de données
                $entityManager->flush();

                // On redirige vers la même page pour actualiser l'affichage
                return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
            }
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}