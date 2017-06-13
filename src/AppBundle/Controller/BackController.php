<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\ProfilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\UserBundle;


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

        if ($form->isSubmitted() && $form->isValid()) {

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
            $observations = $repository->findBy(['author' => $this->getUser()], array("id" => "desc"));
        } else {
            $observations = $repository->findBy(array(), array("id" => "desc"));
        }


        return $this->render('back/observationsDashboard.html.twig', array(
            'observations' => $observations,
        ));
    }

    /**
     * @Route("/dashboard/all_observations", name="dashboard_all_observations")
     * @Method({"GET","POST"})
     */
    public function AllObservationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Observation');

        $observations = $repository->findBy(array(), array("id" => "desc"));


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
            ->setValidateur($this->getUser());
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

    /**
     * @Route("/dashboard/all_articles", name="dashboard_all_articles")
     * @Method({"GET","POST"})
     */
    public function AllArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Article');

        $articles = $repository->findBy(array(), array("id" => "desc"));

        return $this->render('back/allArticlesDashboard.html.twig', array(
            'articles' => $articles,
        ));
    }

    /**
     * @Route("/dashboard/utilisateurs", name="dashboard_utilisateurs")
     * @Method({"GET","POST"})
     */
    public function UtilisateursAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('UserBundle:User');

        $utilisateurs = $repository->findBy(array(), array("id" => "desc"));

        return $this->render('back/utilisateursDashboard.html.twig', array(
            'utilisateurs' => $utilisateurs,

        ));
    }

    /**
     * @Route("/dashboard/detailUtilisateur/{id}", requirements={"id" = "\d+"}, name="detailUtilisateur")
     * @param User $user
     */
    public function voirObservationAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        
        return $this->render('back/detailUtilisateur.html.twig', array(
            'form' => $form->createView()
        ));
 
    }

    /**
     * @Route("/dashboard/utilisateurs/{id}/supprimer", name="supprimerUtilisateurs")
     * @Method({"GET","POST"})
     * @param User $user
     * @ParamConverter()
     */
    public function SupprimerUtilisateursAction(User $user, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();
        $user->setDeleted(new \Datetime());
        $em->flush();

        $this->addFlash('notice', "L'utilisateur a bien été supprimé !");

        if ($redirect === 'utilisateurs') {
            return $this->redirectToRoute('dashboard_utilisateurs');
        }


        return $this->redirectToRoute('', array(
            "id" => $user->getId()));

    }

    /**
     * @Route("/dashboard/utilisateurs/{id}/promouvoir", name="promouvoirUtilisateurs")
     * @Method({"GET","POST"})
     * @param User $user
     * @ParamConverter()
     */
    public function PromouvoirUtilisateursAction(User $user, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();

        if ($user->getDemandeNaturaliste()== NULL){
            $user
                ->setDemandeNaturaliste(new \Datetime())
                ->setRoles(['ROLE_NATURALISTE']);
        }  elseif ($user->getDemandeContributeur()== NULL){
            $user
                ->setDemandeContributeur(new \Datetime())
                ->setRoles(['ROLE_CONTRIBUTEUR']);
        }

        $em->flush();

        $this->addFlash('notice', "L'utilisateur a bien été promu !");

        if ($redirect === 'utilisateurs') {
            return $this->redirectToRoute('dashboard_utilisateurs');
        }

        return $this->redirectToRoute('detailUtilisateur', array(
            "id" => $user->getId()));

    }

    /**
     * @Route("/dashboard/utilisateurs/{id}/destituer", name="destituerUtilisateurs")
     * @Method({"GET","POST"})
     * @param User $user
     * @ParamConverter()
     */
    public function DestituerUtilisateursAction(User $user, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();

        if ($user->getRoles()==['ROLE_NATURALISTE']){
            $user
                ->setDemandeNaturaliste(NULL)
                ->setRoles(['ROLE_PARTICULIER']);
        }  elseif ($user->getRoles()==['ROLE_CONTRIBUTEUR']){
            $user
                ->setDemandeNaturaliste(NULL)
                ->setRoles(['ROLE_NATURALISTE']);
        }

        $em->flush();

        $this->addFlash('notice', "L'utilisateur a bien été rétrogradé !");

        if ($redirect === 'utilisateurs') {
            return $this->redirectToRoute('dashboard_utilisateurs');
        }

        return $this->redirectToRoute('detailUtilisateur', array(
            "id" => $user->getId()));

    }


}
