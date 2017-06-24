<?php

namespace AppBundle\Manager;

class MapManager
{
	private $em;

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function getPublishedObservationsForOiseauName($name)
	{
		$oiseau = $this->em->getRepository('AppBundle:OiseauTaxref')->findOneBy(array(
    			'nomComplet' => $name
    			));

    	$listObservations = $this->em->getRepository('AppBundle:Observation')->findPublishedObsvervationForOiseau($oiseau);

		// On parse l'objet pour retourner un tableau de tableau
		$listObservationsArray = [];
		foreach ($listObservations as $observation) 
		{
			$listObservationsArray[] = [
				'nomOiseau' => $observation->getOiseau()->getNomVern()  .' - ' .$observation->getOiseau()->getNomComplet(),
				'author' => $observation->getAuthor()->getUsername(),
				'date' => $observation->getDate(),
				'content' => $observation->getContent(),
				'longitude' => $observation->getLongitude(),
				'latitude' => $observation->getLatitude(),
				'photo' => $observation->getPhoto(),
				'audio' => $observation->getAudio(),

			];
		}

		return $listObservationsArray;
	}	
}
