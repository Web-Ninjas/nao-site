<?php

namespace AppBundle\Repository;

use InvalidArgumentException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\User;

/**
 * ObservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ObservationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findObsvervationForOiseau($oiseau)
    {
        $qd = $this->createQueryBuilder('o')
        ->where('o.oiseau = :oiseau')
        ->setParameter('oiseau', $oiseau)
        ->leftJoin('o.oiseau', 'bird')
        ->addSelect('bird');

        return $qd
            ->getQuery()
            ->getResult();
    }

    public function findAllWithOiseau()
    {
        $qd = $this->createQueryBuilder('o')
        ->leftJoin('o.oiseau', 'bird')
        ->addSelect('bird');

        return $qd
            ->getQuery()
            ->getResult();
    }

    public function countNbObservations()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('COUNT(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function listeObservationsNonSupprimer(User $user = null)
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->orderBy('o.id', 'desc')
            ->andWhere('o.deleted IS NULL');

        if ($user !== null) {
            $qb->andWhere('o.author = :author');
            $qb->setParameter('author', $user);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère une liste d'observations triés et paginés.
     *
     * @param int $page Le numéro de la page
     * @param int $nbMaxParPage Nombre maximum d'observation par page
     *
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Paginator
     */
    public function findAllPagineEtTrie($page, $nbMaxParPage, User $user = null, $filtre = null, $ordreDeTri = 'DESC')
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($nbMaxParPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').'
            );
        }

        $qb = $this->createQueryBuilder('o')
            ->select('o, a, oiseau')
            ->where('CURRENT_DATE() >= o.date')

            ->join('o.author', 'a')
            ->join('o.oiseau', 'oiseau')
            ->leftJoin('o.validateur', 'v')
            ->andWhere('o.deleted IS NULL');


        if ($user !== null) {
            $qb->andWhere('o.author = :author');
            $qb->setParameter('author', $user);
        }

        if (isset($filtre)) {
            $mapping = [
                'id' => 'o.id',
                'author' => 'a.username',
                'status' => 'o.status',
                'date' => 'o.date',
                'validateur' => 'v.username',
                'oiseau' => 'oiseau.nomVern'
            ];

            $qb->orderBy($mapping[$filtre], $ordreDeTri);
        } else {
            $qb->orderBy('o.date', 'DESC');
        }


        $query = $qb->getQuery();


        $premierResultat = ($page - 1) * $nbMaxParPage;
        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($query);

        if ( ($paginator->count() <= $premierResultat) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }
}
