<?php

namespace AppBundle\Repository;

use InvalidArgumentException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\User;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
    public function countNbArticles()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a.id)');
          

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function listeArticlesNonSupprimer(User $user = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->orderBy('a.id', 'desc')
            ->andWhere('a.deleted IS NULL');

        if ($user !== null) {
            $qb->andWhere('o.author = :author');
            $qb->setParameter('author', $user);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère une liste d'articles triés et paginés.
     *
     * @param int $page Le numéro de la page
     * @param int $nbMaxParPage Nombre maximum d'article par page
     *
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Paginator
     */
    public function findAllPagineEtTrie($page, $nbMaxParPage)
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

        $qb = $this->createQueryBuilder('a')
            ->where('CURRENT_DATE() >= a.date')
            ->orderBy('a.date', 'DESC');

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
