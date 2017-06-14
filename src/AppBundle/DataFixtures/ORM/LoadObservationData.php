<?php
// src/AppBundle/DataFixtures/ORM/LoadObservationData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use AppBundle\Entity\Observation;
use AppBundle\Entity\OiseauTaxref;
use UserBundle\Entity\User;

class LoadObservationData implements FixtureInterface
{

	private $em;

    public function load(ObjectManager $manager)
    {
    	$this->em = $manager;

    	Fixtures::load(__DIR__.'/fixturesObservation.yml', $manager, array(
    		'providers' => [$this]
    		));
    }

    public function status()
    {
    	$listStatus = [
    		Observation::A_VALIDER,
    		Observation::VALIDEE,
    		Observation::A_MODIFIER,
    		Observation::SIGNALEE,
    		Observation::SUPPRIMEE
    		];

    	$key = array_rand($listStatus);

    	return $listStatus[$key];
    }

    public function oiseau()
    {
    	return $this->em->getRepository('AppBundle:OiseauTaxref')->findOneBy(
    		array('id' => rand(1, 5027))
    		);
    }

    public function user()
    {
    	return $this->em->getRepository('UserBundle:User')->findOneBy(
    		array('id' => rand(236, 247))
    		);
    }
    
}
