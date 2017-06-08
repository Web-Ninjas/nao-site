<?php
// src/AppBundle/DataFixtures/ORM/LoadArticleData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use Nelmio\Alice\Fixtures;

class LoadArticleData implements FixtureInterface
{


    public function load(ObjectManager $manager)
    {

    	Fixtures::load(__DIR__.'/fixtures.yml', $manager, array(
    		'providers' => [$this]
    		));
    }

    public function getOrder()
    {
    	return 2;
    }

    /*
    public function author()
    {
    	$user = new User();
    	$user->setRole('admin');
    	$user->setUsername('admiiin');
    	$user->setName('radius_server_secret(radius_handle)');
    	$user->setFirstName('Fred');
    	$user->setBirthDate('1988-05-19');
    	$user->setEmail('lalala@gmail.com');
    	$user->setPassword('@dmin');

    	return $user;
    }
    */
}