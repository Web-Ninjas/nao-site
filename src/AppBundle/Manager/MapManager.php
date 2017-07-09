<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Observation;
use Doctrine\ORM\EntityManagerInterface;

class MapManager
{
	/** @var EntityManagerInterface */
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param $name
	 * @param $periode
	 * @return array
	 */
	public function getPublishedObservationsForOiseauNameAndDate($name, $periode)
	{
		$minDate = null;

		if ($periode == '(Origine)') {
			$minDate = null;
		} else {
			$today = new \DateTime('now'); 
			$today = $today->format('Y-m-d');
			switch ($periode) {
				case "(1 jour)":
					$minDate = strtotime($today .' -1 day');
					break;
				
				case "(1 semaine)":
					$minDate = strtotime($today .' -1 week');
					break;

				case "(1 mois)":
					$minDate = strtotime($today .' -1 month');
					break;

				case "(3 mois)":
					$minDate = strtotime($today .' -3 months');
					break;

				case "(6 mois)":
					$minDate = strtotime($today .' -6 months');
					break;

				case "(1 an)":
					$minDate = strtotime($today .' -1 year');
					break;
			}

		// Convert minDate to DateTime
		$minDate = date("Y-m-d H:i:s", $minDate);
		}

		$oiseau = $this->em->getRepository('AppBundle:OiseauTaxref')->findOneBy(array(
    			'nomComplet' => $name
    			));

    	$listObservations = $this->em->getRepository('AppBundle:Observation')->findPublishedObsvervationForOiseau($oiseau, $minDate);

		// On parse l'objet pour retourner un tableau de tableau
		$listObservationsArray = $this->arrayOfObjectsToArrayOfArray($listObservations);

		return $listObservationsArray;
	}

	/**
	 * @param Observation[] $list
	 * @return array
	 */
	public function arrayOfObjectsToArrayOfArray($list)
	{
		$listObservationsArray = array();

		foreach ($list as $observation) {
			// Si il y aune photo on note le chemin sinon on le met à null
			if ($observation->getPhotoExtension() != null) {
				$photoPath = '/nao-site/web/' .$observation->getPhotoWebPath();
			} else {
				$photoPath = null;
			}

			$listObservationsArray[] = [
				'id' => $observation->getId(),
				'nomOiseau' => is_null($observation->getOiseau()->getNomVern() ) ? $observation->getOiseau()->getNomComplet() : $observation->getOiseau()->getNomVern(),
				'author' => $observation->getAuthor()->getUsername(),
				'date' => $observation->getDate(),
				'content' => $observation->getContent(),
				'longitude' => $observation->getLongitude(),
				'latitude' => $observation->getLatitude(),
				'photoPath' => $photoPath,
				'audioPath' => '/nao-site/web/' .$observation->getAudioWebPath(),

			];
		}

		return $listObservationsArray;
	}
}
