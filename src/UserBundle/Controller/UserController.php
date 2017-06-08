<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method({"GET","POST"})
     */
    public function loginAction()
    {
        // Si le visiteur est déjà loggé on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
        	return $this->redirectToRoute('homepage');
        }

        // AuthUtils récupère le nom de l'utilisateur ou l'erreur si formulaire est invalide
        $authUtils = $this->get('security.authentication_utils');

        return $this->render('front/login.html.twig', [
        	'last_username' => $authUtils->getLastUsername(),
        	'error' => $authUtils->getLastAuthenticationError()
        	]);
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function createAccountAction(Request $request)
    {
    	$user = new User();
    	$form = $this->createForm(UserType::class, $user);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid() )
    	{
    		// Si l'utilisateur a demandé à être naturaliste on modifie la propriété demandeNaturaliste en DateTime
    		if ($request->getContent('demandeNaturaliste') === '1')
    		{
    			$user->setDemandeNaturaliste(new \DateTime('now'));
    		} else {
    			$user->setDemandeNaturaliste(null);
    		}

    		// On enregistre en bdd
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($user);
    		$em->flush();

    		return $this->redirectToRoute('login');
    	}

    	return $this->render('front/inscription.html.twig', array(
    		'form' => $form->createView()
    		));
    }

}
