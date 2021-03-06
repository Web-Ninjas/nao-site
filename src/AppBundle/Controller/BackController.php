<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Observation;
use AppBundle\Entity\Page;
use AppBundle\Form\AdminType;
use AppBundle\Form\ArticleType;
use AppBundle\Form\DemandeModifObservationType;
use AppBundle\Form\ObservationType;
use AppBundle\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UserBundle\Entity\User;
use UserBundle\Form\ModifMdp;


class BackController extends Controller
{
    /**
     * @Route("/dashboard/menu", name="dashboard_menu")
     * @Method({"GET"})
     * @Security("has_role('ROLE_PARTICULIER') ")
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mesObsCount = $em->getRepository('AppBundle:Observation')->countNbObservations($this->getUser());
        $allObsCount = $em->getRepository('AppBundle:Observation')->countNbObservations();
        $mesArtCount = $em->getRepository('AppBundle:Article')->countNbArticles($this->getUser());
        $allArtCount = $em->getRepository('AppBundle:Article')->countNbArticles();
        $allUsersCount = $em->getRepository('UserBundle:User')->countNbUsers();

        $masterRequest = $this->get('request_stack')->getMasterRequest();

        return $this->render('back/menu.html.twig', [
            'routeActuelle' => $masterRequest->attributes->get('_route'),
            'allObsCount' => $allObsCount,
            'mesObsCount' => $mesObsCount,
            'mesArtCount' => $mesArtCount,
            'allArtCount' => $allArtCount,
            'allUsersCount' => $allUsersCount
        ]);
    }


    /**
     * @Route("/dashboard/profil", name="dashboard_profil")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_PARTICULIER') ")
     */
    public function profilAction(Request $request)
    {
        $user = $this->getUser();

        // Faire le formulaire et l'envoyer à la vue pour l'affichage !

        $form = $this->get('form.factory')->create(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $estNaturalite = $this->get('security.authorization_checker')->isGranted('ROLE_NATURALISTE');

            if ($estNaturalite == false) {
                // Si l'utilisateur a demandé à être naturaliste on modifie la propriété demandeNaturaliste en DateTime
                if ($form->has('isNaturaliste')) {
                    if ($form->get('isNaturaliste')->getData() == 1) {
                        $user->setDemandeNaturaliste(new \DateTime('now'));
                    } else {
                        $user->setDemandeNaturaliste(null);
                    }
                }
            }

            $estContributeur = $this->get('security.authorization_checker')->isGranted('ROLE_CONTRIBUTEUR');

            if ($estContributeur == false) {
                // Si l'utilisateur a demandé à être naturaliste on modifie la propriété demandeNaturaliste en DateTime
                if ($form->has('isContributeur')) {
                    if ($form->get('isContributeur')->getData() == 1) {
                        $user->setDemandeContributeur(new \DateTime('now'));
                    } else {
                        $user->setDemandeContributeur(null);
                    }
                }
            }

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Votre profil a bien été modifié !');

            return $this->redirectToRoute('dashboard_profil');
        }

        return $this->render('back/profilDashboard.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/dashboard/modifMdp", name="dashboard_modifMdp")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_PARTICULIER') ")
     */
    public function modifMdpAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $form = $this->get('form.factory')->create(ModifMdp::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été modifié !');

            return $this->redirectToRoute('dashboard_profil');
        }

        return $this->render(':back:modifMdp.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/dashboard/observations", name="dashboard_observations")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_PARTICULIER') ")
     */
    public function observationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Observation');

        $observations = $repository->listeObservationsNonSupprimer($this->getUser());

        return $this->render('back/observationsDashboard.html.twig', array(
            'observations' => $observations,
        ));
    }

    /**
     * @Route("/dashboard/all_observations", name="dashboard_all_observations")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_NATURALISTE') ")
     */
    public function allObservationsAction(Request $request)
    {
        $form = $this->get('form.factory')->create(DemandeModifObservationType::class);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Observation');

        $observations = $repository->listeObservationsNonSupprimer();

        return $this->render('back/allObservationsDashboard.html.twig', array(
            'observations' => $observations,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/dashboard/observations/{id}/valider", requirements={"id" = "\d+"}, name="validerObservation")
     * @Method({"GET","POST"})
     * @param Observation $observation
     * @ParamConverter()
     * @Security("has_role('ROLE_NATURALISTE') ")
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
     * @Security("has_role('ROLE_NATURALISTE') ")
     */
    public function signalerObservationAction(Observation $observation, Request $request)
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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
     * @Route("/dashboard/articles", name="dashboard_articles")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_CONTRIBUTEUR') ")
     */
    public function articlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Article');

        $articles = $repository->listeArticlesNonSupprimer($this->getUser());

        return $this->render('back/artilcesDashboard.html.twig', array(
            'articles' => $articles,
        ));
    }


    /**
     * @Route("/dashboard/all_articles", name="dashboard_all_articles")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function allArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Article');

        $articles = $repository->listeArticlesNonSupprimer();

        return $this->render('back/allArticlesDashboard.html.twig', array(
            'articles' => $articles,
        ));
    }

    /**
     * @Route("/dashboard/article/{slug}/supprimer", name="supprimerArticle")
     * @Method({"GET","POST"})
     * @param Article $article
     * @ParamConverter()
     * @Security("has_role('ROLE_CONTRIBUTEUR') ")
     */
    public function supprimerArticleAction(Article $article, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();
        $article
            ->setDeleted(new \Datetime());
        $em->flush();

        $this->addFlash('notice', "L'article a bien été supprimé !");

        if ($redirect === 'all_articles') {
            return $this->redirectToRoute('dashboard_all_articles');
        }

        if ($redirect === 'actualites') {
            return $this->redirectToRoute('actualites');
        }

        return $this->redirectToRoute('article', array(
            "slug" => $article->getSlug()));
    }


    /**
     * @Route("/dashboard/utilisateurs{page}", defaults={"page" = "1" } ,requirements={"id" = "\d+"}, name="dashboard_utilisateurs")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function UtilisateursAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('UserBundle:User');

        $utilisateurs = $repository->findAllTrie();

        //var_dump($utilisateurs); die;
        return $this->render('back/utilisateursDashboard.html.twig', array(
            'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * @Route("/dashboard/detailUtilisateur/{id}", requirements={"id" = "\d+"}, name="detailUtilisateur")
     * @param User $user
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function voirUtilisateurAction(User $user, Request $request)
    {
        $form = $this->get('form.factory')->create(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('back/detailUtilisateur.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/dashboard/utilisateurs/{id}/supprimer", name="supprimerUtilisateurs")
     * @Method({"GET","POST"})
     * @param User $user
     * @ParamConverter()
     * @Security("has_role('ROLE_ADMIN') ")
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
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function PromouvoirUtilisateursAction(User $user, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();

        if (in_array('ROLE_PARTICULIER', $user->getRoles())) {
            $user->setRoles(['ROLE_NATURALISTE']);
            $user->setDemandeNaturaliste(NULL);

        } elseif (in_array('ROLE_NATURALISTE', $user->getRoles())) {
            $user->setRoles(['ROLE_CONTRIBUTEUR']);
            $user->setDemandeContributeur(NULL);
        }

        $em->flush();

        $this->addFlash('notice', "L'utilisateur a bien été promu !");

        if ($redirect === 'utilisateurs') {
            return $this->redirectToRoute('dashboard_utilisateurs');
        }

        return $this->redirectToRoute('detailUtilisateur', array(
            "id" => $user->getId(),
        ));
    }

    /**
     * @Route("/dashboard/utilisateurs/{id}/destituer", name="destituerUtilisateurs")
     * @Method({"GET","POST"})
     * @param User $user
     * @ParamConverter()
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function DestituerUtilisateursAction(User $user, Request $request)
    {
        $redirect = $request->query->get('redirect');

        $em = $this->getDoctrine()->getManager();

        if (in_array('ROLE_NATURALISTE', $user->getRoles())) {
            $user->setDemandeNaturaliste(NULL);
            $user->setNomEntreprise(NULL);
            $user->setNSiret(NULL);
            $user->setRoles(['ROLE_PARTICULIER']);
        } elseif (in_array('ROLE_CONTRIBUTEUR', $user->getRoles())) {
            $user->setDemandeContributeur(NULL);
            $user->setRoles(['ROLE_NATURALISTE']);
        }

        $em->flush();

        $this->addFlash('notice', "L'utilisateur a bien été rétrogradé !");

        if ($redirect === 'utilisateurs') {
            return $this->redirectToRoute('dashboard_utilisateurs');
        }

        return $this->redirectToRoute('detailUtilisateur', array(
            "id" => $user->getId()));
    }

    /**
     * @Route("/dashboard/rediger/article", name="dashboard_redigerArticle")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_CONTRIBUTEUR') ")
     */
    public function redigerArticleAction(Request $request)
    {
        $article = new Article();
        $article
            ->setDate(new \Datetime())
            ->setAuthor($this->getUser());

        $form = $this->get('form.factory')->create(ArticleType::class, $article, array(
            'validation_groups' => array('default', 'ajout')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', "L'article a bien été enregistré, celle-ci va être validé par l'admin avant d'être publié!");

            return $this->redirectToRoute('dashboard_articles');
        }

        return $this->render(':back:redigerArticle.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/dashboard/administration/{identifier}", name="dashboard_administration", defaults={"identifier" = "accueil"})
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function adminAction(Request $request, $identifier)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBy(
            array('nameIdentifier' => $identifier)
        );

        $page->setLastUpdate(new \Datetime());
        
        $form = $this->get('form.factory')->create(AdminType::class, $page);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $this->addFlash('notice', 'La page ' . $page->getTitle() . ' a bien été enregistrée !');

            return $this->redirectToRoute('dashboard_administration', [
                'identifier' => $identifier
            ]);
        }

        return $this->render(':back:adminDashboard.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/dashboard/editArticle/{article}", name="dashboard_editArticle")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_CONTRIBUTEUR') ")
     */
    public function editArticleAction(Article $article, Request $request)
    {
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'L\'article a bien été modifié !');

            return $this->redirectToRoute('article', array(
                'id' => $article->getId()
            ));
        }

        return $this->render(':back:editArticle.html.twig', array(
            'form' => $form->createView(),
            'article' => $article
        ));
    }

    /**
     * @Route("/dashboard/editObservation/{observation}", name="dashboard_editObservation")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_PARTICULIER') ")
     */
    public function editObservationAction(Observation $observation, Request $request)
    {
        /*
        $listObservations[] = $observation;
        $listObservations = $this->get("app.manager.map")->arrayOfObjectsToArrayOfArray($listObservations);
        */

        $form = $this->get('form.factory')->create(ObservationType::class, $observation);
        $form->handleRequest($request);

        // Liste les noms des oiseaux pour l'autocomplete
        $em = $this->getDoctrine()->getManager();
        $listOiseaux = $em->getRepository('AppBundle:OiseauTaxref')->findAll();
        $listOiseauNames = [];
        // Met en forme les noms pour l'autocomplete
        foreach ($listOiseaux as $oiseau) {
            $listOiseauNames[] = $oiseau->getNomVern() . ' - ' . $oiseau->getNomComplet();
        }

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère l'oiseau en bdd d'après son nom formatté dans la barre de recherche autocomplete
            $nomOiseau = $request->request->get('appbundle_observation')['nomOiseau'];
            if (strpos($nomOiseau, '-') !== false) {
                $nomOiseau = substr($nomOiseau, strpos($nomOiseau, "-") + 2);
            }

            $oiseau = $em->getRepository('AppBundle:OiseauTaxref')->findOneBy([
                'nomComplet' => $nomOiseau
            ]);
            $observation->setOiseau($oiseau);


            // Si l'utilisateur est au moins naturaliste, son observation est validée tout de suite
            $isNaturaliste = $this->get('security.authorization_checker')->isGranted('ROLE_NATURALISTE');
            if ($isNaturaliste) {
                $observation->setStatus(Observation::VALIDEE);
                $observation->setPublish(new \DateTime('now'));

                $this->addFlash('notice', 'Merci d\'avoir soumis une observation, celle-ci vient d\'être publiée');
            } else {
                $observation->setStatus(Observation::A_VALIDER);
            }

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'L\'observation a bien été modifiée !');

            return $this->redirectToRoute('observation', array(
                'id' => $observation->getId()
            ));
        }

        return $this->render(':back:editObservation.html.twig', array(
            'form' => $form->createView(),
            'listOiseauNames' => $listOiseauNames,
            'observation' => $observation
        ));
    }

    /**
     * @Route("/dashboard/article/{slug}/publier", name="dashboard_publier-article")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function publierActicleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $article->setPublished(new \Datetime());
        $em->flush();

        $this->addFlash('notice', "L'acticle a bien été publié !");

        return $this->redirectToRoute('dashboard_all_articles', array(
            "slug" => $article->getSlug()));
    }

    /**
     * @Route("/dashboard/article/{slug}/depublier", name="dashboard_depublier-article")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function depublierActicleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $article->setPublished(null);
        $em->flush();

        $this->addFlash('notice', "L'acticle a bien été dépublié !");

        return $this->redirectToRoute('dashboard_all_articles', array(
            "slug" => $article->getSlug()));
    }
}

