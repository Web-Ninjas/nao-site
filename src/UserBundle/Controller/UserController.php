<?php

namespace UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Form\ReinitMdpType;
use UserBundle\Form\ResetType;
use UserBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Manager\MailReinitMdpManager;


class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method({"GET","POST"})
     */
    public function loginAction()
    {
        // Si le visiteur est déjà loggé on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
    public function createAccountAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
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
     * @Route("/reset", name="reset")
     * @Method({"GET","POST"})
     */
    public function resetAction(Request $request)
    {
        $mailer = $this->get("app.manager.mailReinit");
        $form = $this->createForm(ResetType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uniqid = uniqid();

            $em = $this->getDoctrine();

            $user = $em->getRepository('UserBundle:User')->findOneBy(['email' => $form->getData()]);
            $user->setTokenRegenerationMotDePasse($uniqid);

            $em->getManager()->flush();

            // Envoi du mail à l'utilisateur avec le token ($uniqid)
            $mailer->envoyerMailReinitMdp($user);
            

            $this->addFlash('notice', 'Demande de modification envoyée par e-mail');
        }

        return $this->render(':front:reset.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/reinit-mdp/{token}", name="reinit-mdp")
     * @Method({"GET","POST"})
     */
    public function reinitMdpAction($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $em = $this->getDoctrine();

        $user = $em->getRepository('UserBundle:User')->findOneBy(['tokenRegenerationMotDePasse' => $token]);

        if ($user == null) {
            return $this->createNotFoundException('Token non trouvé : '.$token);
        }

        // propose le formulaire demandant 2 fois le nouveau mot de passe
        $form = $this->createForm(ReinitMdpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $user->setTokenRegenerationMotDePasse(null);
            $em->getManager()->flush();


            // dire à l'utilisateur que son mot de passe est changé !
            $this->addFlash('notice', 'Votre mot de passe à bien été modifié');

            return $this->redirectToRoute('login');
            
        }
        
        // affichage du formulaire
        return $this->render(':front:reinitMdp.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    
}
