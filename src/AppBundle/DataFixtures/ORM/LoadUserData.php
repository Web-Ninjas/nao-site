<?php
// src/AppBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;
use Nelmio\Alice\Fixtures;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
    	Fixtures::load(__DIR__.'/fixtures.yml', $manager);
    }

}
