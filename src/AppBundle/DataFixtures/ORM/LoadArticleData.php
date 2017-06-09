<?php
// src/AppBundle/DataFixtures/ORM/LoadArticleData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;
use UserBundle\Entity\User;
use Nelmio\Alice\Fixtures;

class LoadArticleData implements FixtureInterface
{


    public function load(ObjectManager $manager)
    {

    	Fixtures::load(__DIR__.'/fixtures.yml', $manager, array(
    		'providers' => [$this]
    		));
    }
    
}
