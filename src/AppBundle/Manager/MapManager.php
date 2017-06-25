<?php

namespace AppBundle\Manager;

class MapManager
{
	private $em;

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function getPublishedObservationsForOiseauNameAndDate($name, $periode)
	{
		$minDate;
		if ($periode == '(Origine)') {
			$minDate = 0;
		} else {
			$today = new \DateTime('now'); 
			$today->format('Y-m-d');
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
		}

		$oiseau = $this->em->getRepository('AppBundle:OiseauTaxref')->findOneBy(array(
    			'nomComplet' => $name
    			));

    	$listObservations = $this->em->getRepository('AppBundle:Observation')->findPublishedObsvervationForOiseau($oiseau, $minDate);

		// On parse l'objet pour retourner un tableau de tableau
		$listObservationsArray = $this->arrayOfObjectsToArrayOfArray($listObservations);

		return $listObservationsArray;
	}	

	public function arrayOfObjectsToArrayOfArray($list)
	{
		$listObservationsArray = array();

		foreach ($list as $observation) 
		{
			$listObservationsArray[] = [
				'nomOiseau' => is_null($observation->getOiseau()->getNomVern() ) ? $observation->getOiseau()->getNomComplet() : $observation->getOiseau()->getNomVern(),
				'author' => $observation->getAuthor()->getUsername(),
				'date' => $observation->getDate(),
				'content' => $observation->getContent(),
				'longitude' => $observation->getLongitude(),
				'latitude' => $observation->getLatitude(),
				'photo' => $observation->getPhotoWebPath(),
				'audio' => $observation->getAudioWebPath(),

			];
		}

		return $listObservationsArray;
	}
}
