<?php

namespace UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;


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
    public function createAccountAction(Request $request,UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
    	$user = new User();
    	$form = $this->createForm(UserType::class, $user);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			
    		// Si l'utilisateur a demandé à être naturaliste on modifie la propriété demandeNaturaliste en DateTime
    		if ($form->get('isNaturaliste')->getData() == 1) {
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

	/**
	 * @Method({"GET"})
	 * @Route("/reset", name="reset")
	 * @Template(":front:reset.html.twig")
	 */
	public function reset() {
		return array();
	}

	/**
	 * @Route("/reset")
	 * @Method({"GET","POST"})
	 */
	public function resetAction(Request $request)
	{

		$params = $request->request->all();
		if (!array_key_exists("login", $params)) {
			throw new \Exception("No login given");
		}
		$login = &$params["login"];
		$em = $this->container->get("doctrine.orm.default_entity_manager");
		$user = $em->getRepository("NamespaceMyBundle:User")->findOneBy(array("login" => $login));
		
		if ($user == null) {
			return $this->redirect($this->generateUrl("login", array()));
		}
		
		$password = "myRandowPassword";
		$user->setPassword($this->container->get("security.encoder_factory")->getEncoder($user)->encodePassword($password, $user->getSalt()));
		$em->persist($user);
		$em->flush();
		
		// On envoie le mot de passe par mail
		
		return $this->redirect($this->generateUrl("login", array()));
	}

}
