<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProfilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class BackController extends Controller
{
    /**
     * @Route("/dashboard/profil", name="profil")
     * @Method({"GET","POST"})
     */
    public function profilAction(\Symfony\Component\HttpFoundation\Request $request)
    {

        $user = $this->getUser();

        // Faire le formulaire et l'envoyer à la vue pour l'affichage !

        $form = $this->get('form.factory')->create(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() )
        {
            /*// Si l'utilisateur a demandé à être naturaliste on modifie la propriété demandeNaturaliste en DateTime
            if ($request->getContent('demandeNaturaliste') === '1')
            {
                $user->setDemandeNaturaliste(new \DateTime('now'));
            } else {
                $user->setDemandeNaturaliste(null);
            }*/

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('profil');
        }

        $this->addFlash('notice', 'Votre profil a bien été modifié !');

        return $this->render('back/profilDashboard.html.twig', array(
            'form' => $form->createView()
        ));
    }
}