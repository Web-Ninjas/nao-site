<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\ProfilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class BackController extends Controller
{
    /**
     * @Route("/dashboard/profil", name="dashboard_profil")
     * @Method({"GET","POST"})
     */
    public function profilAction(Request $request)
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

            return $this->redirectToRoute('dashboard_profil');
        }

        $this->addFlash('notice', 'Votre profil a bien été modifié !');

        return $this->render('back/profilDashboard.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/dashboard/observations", name="dashboard_observations")
     * @Method({"GET","POST"})
     */
    public function observationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Observation');

        $isSeulementMoi = $request->query->has('only-me');


        if ($isSeulementMoi) {
            $observations = $repository->findBy(['author' => $this->getUser()],array("id" => "desc"));
        } else {
            $observations = $repository->findAll();
        }


        return $this->render('back/observationsDashboard.html.twig', array(
            'observations' => $observations,
        ));
    }

    /**
     * @Route("/dashboard/all_observations", name="dashboard_all_observations")
     * @Method({"GET","POST"})
     */
    public function AllObservationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Observation');

        $isSeulementMoi = $request->query->has('only-me');


        if ($isSeulementMoi) {
            $observations = $repository->findBy(['author' => $this->getUser()],array("id" => "desc"));
        } else {
            $observations = $repository->findAll();
        }


        return $this->render('back/allObservationsDashboard.html.twig', array(
            'observations' => $observations,
        ));
    }

    /**
     * @Route("/dashboard/observations/{id}/valider", requirements={"id" = "\d+"}, name="validerObservation")
     * @Method({"GET","POST"})
     * @param Observation $observation
     * @ParamConverter()
     */
    public function validerObservationAction(Observation $observation, Request $request)
    {
        $redirect = $request->query->get('redirect');
        
        $em = $this->getDoctrine()->getManager();
        $observation
            ->setStatus(Observation::VALIDEE)
            ->setPublish(new \Datetime())
            ->setValidateur($this->getUser());
        $em->flush();

        $this->addFlash('notice', "L'observation a bien été validée !");
        
        if ($redirect === 'all_observations') {
            return $this->redirectToRoute('dashboard_all_observations');
        }
        
        return $this->redirectToRoute('observation', array(
            "id" => $observation->getId()));

    }

    /**
     * @Route("/dashboard/observations/{id}/signaler", requirements={"id" = "\d+"}, name="signalerObservation")
     * @Method({"GET","POST"})
     * @param Observation $observation
     * @ParamConverter()
     */
    public function signalerAction(Observation $observation, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();
        $observation
            ->setStatus(Observation::SIGNALEE)
            ->setValidateur($this->getUser());;
        $em->flush();

        $this->addFlash('notice', "L'observation a bien été signalée !");

        if ($redirect === 'all_observations') {
            return $this->redirectToRoute('dashboard_all_observations');
        }

        return $this->redirectToRoute('observation', array(
            "id" => $observation->getId())
        );

    }

    /**
     * @Route("/dashboard/observations/{id}/supprimer", requirements={"id" = "\d+"}, name="supprimerObservation")
     * @Method({"GET","POST"})
     * @param Observation $observation
     * @ParamConverter()
     */
    public function supprimerObservationAction(Observation $observation, Request $request)
    {
        $redirect = $request->query->get('redirect');
        
        $em = $this->getDoctrine()->getManager();
        $observation
            ->setStatus(Observation::SUPPRIMEE)
            ->setDeleted(new \Datetime())
            ->setValidateur($this->getUser());
        $em->flush();

        $this->addFlash('notice', "L'observation a bien été supprimée !");

        if ($redirect === 'all_observations') {
            return $this->redirectToRoute('dashboard_all_observations');
        }

        if ($redirect === 'observations') {
            return $this->redirectToRoute('dashboard_observations');
        }

        return $this->redirectToRoute('observation', array(
            "id" => $observation->getId()));

    }
}