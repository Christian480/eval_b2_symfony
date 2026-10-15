<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Récupère les événements validés, à venir, et filtrés par catégorie si besoin.
     */
    public function findHomeEvents(?string $category = null): array
    {
        // On crée un constructeur de requête (alias 'e' pour event)
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.isValidated = :validee')
            ->setParameter('validee', true)
            ->andWhere('e.date >= :maintenant')
            ->setParameter('maintenant', new \DateTime()) // Date du jour
            ->orderBy('e.date', 'ASC'); // Tri par date croissante (le plus proche en premier)

        // Si une catégorie est cliquée dans l'URL, on ajoute un filtre
        if ($category) {
            $query->andWhere('e.category = :cat')
                  ->setParameter('cat', $category);
        }

        return $query->getQuery()->getResult();
    }
}