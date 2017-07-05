<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Observation;
use AppBundle\Form\ContactType;
use AppBundle\Form\Model\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\ObservationType;

class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET","POST"})
     */
    public function indexAction(Request $request)
    {
            $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBy(
            	array('nameIdentifier' => 'accueil')          	
            	);

            return $this->render('front/index.html.twig', array(
                'page' => $page,
            ));
    }

    /**
     * @Route("/contact", name="contact")
     * @Method({"GET","POST"})
     */
    public function ContactAction(Request $request)
    {
        $mailer = $this->get("app.manager.mailContact");
        $contact = new Contact();

        $form = $this->get('form.factory')->create(ContactType::class, $contact);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

            $mailer->envoyerMailContact($contact);
                $this->addFlash('notice', 'Votre message a bien été envoyé !');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('front/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentionsAction()
    {

        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBy(
        	array('nameIdentifier' => 'mentions-legales')
        	);

        return $this->render('front/mentions.html.twig', array(
            'page' => $page,
        ));
    }

    /**
     * @Route("/actualites/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="actualites")
    */
    public function newsAction($page)
    {
        $nbNewsParPage = $this->container->getParameter('front_nb_news_par_page');

    	// On cherche les 4 premiers articles pour les afficher
    	$em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Article');

        $listArticles = $repository->findAllPagineEtTrie($page, $nbNewsParPage);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($listArticles) / $nbNewsParPage),
            'nomRoute' => 'actualites',
            'paramsRoute' => array()
        );


    	return $this->render('front/news.html.twig', array(
    		'listArticles' => $listArticles,
            'pagination' => $pagination
    		));
    }

    /**
     * @Route("/article/{slug}", name="article")
     * @param Article $article
     */
    public function voirNewsAction(Article $article)
    {
        // On cherche les 3 premiers articles pour les afficher
        $em = $this->getDoctrine()->getManager();
        $listArticles = $em->getRepository('AppBundle:Article')
            ->findBy(
                array('deleted' => null),
                array('date' => 'desc'),
                3
            );

        return $this->render('front/voirNews.html.twig', array(
            'listArticles' => $listArticles,
            'article'=>$article,
        ));
    }

    /**
     * @Route("/observation/{id}", requirements={"id" = "\d+"}, name="observation")
     * @param Observation $observation
     */
    public function voirObservationAction(Observation $observation)
    {
        return $this->render('front/voirObservation.html.twig', array(
            'observation' => $observation,
        ));
    }

    /**
     * @Route("/qui sommes nous", name="about")
     */
    public function aboutAction()
    {

        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBy(
            array('nameIdentifier' => 'about')
        );

        return $this->render('front/about.html.twig', array(
            'page' => $page,
        ));
    }

    /**
    * @Route("/rechercher", name="map")
    */
    public function mapAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

    	// On requête les observations en ajax
    	if ($request->isXmlHttpRequest() ) 
    	{
    		$periode = $request->request->get('periode');
    		$oiseauName = $request->request->get('oiseauName');
    		$nomOiseauComplet = substr($oiseauName, strpos($oiseauName, "-") + 2); 
    		$listObservationsArray = $this->get("app.manager.map")->getPublishedObservationsForOiseauNameAndDate($nomOiseauComplet, $periode);

    		return new JsonResponse($listObservationsArray);
    	}

    	// On retourne toutes les observations au départ
    	$listObservations = $em->getRepository('AppBundle:Observation')->findAllPublishedWithOiseauAndAuthor();
    	$listObservations = $this->get("app.manager.map")->arrayOfObjectsToArrayOfArray($listObservations);

    	// Liste les noms des oiseaux pour l'autocomplete
    	$listOiseaux = $em->getRepository('AppBundle:OiseauTaxref')->findAll();
    	$listOiseauNames = [];

    	foreach ($listOiseaux as $oiseau) {
    			$listOiseauNames[] = $oiseau->getNomVern() .' - ' .$oiseau->getNomComplet();
    	}

    	return $this->render('front/map.html.twig', [
    		'observations' => json_encode($listObservations),
    		'listOiseauNames' => $listOiseauNames
    		]);
    }

    /**
    * @Route("/observer", name="observer")
    */
    public function observerAction(Request $request)
    {
        // rediriger le user
        $isUserAllowed = $this->get('security.authorization_checker')->isGranted('ROLE_PARTICULIER');
        if (!$isUserAllowed) {
            $this->addFlash('notice', 'Veuillez vous inscrire ou vous connecter pour soumettre une observation');
            return $this->redirectToRoute('login');
        }

        // Créé le formulaire avec l'utilisateur comme auteur
    	$observation = new Observation();
    	$observation->setAuthor($this->getUser());
    	$form = $this->createForm(ObservationType::class, $observation
            , array(
                'validation_groups' => array('default', 'ajout')
            ));
    	$form->handleRequest($request);

    	// Liste les noms des oiseaux pour l'autocomplete
    	$em = $this->getDoctrine()->getManager();
    	$listOiseaux = $em->getRepository('AppBundle:OiseauTaxref')->findAll();
    	$listOiseauNames = [];
        // Met en forme les noms pour l'autocomplete
    	foreach ($listOiseaux as $oiseau) {
    			$listOiseauNames[] = $oiseau->getNomVern() .' - ' .$oiseau->getNomComplet();
    	}

    	if ($form->isSubmitted() & $form->isValid() )
    	{
            // On récupère l'oiseau en bdd d'après son nom formatté dans la barre de recherche autocomplete
    		$nomOiseau = $request->request->get('appbundle_observation')['nomOiseau'];
    		$nomOiseauComplet = substr($nomOiseau, strpos($nomOiseau, "-") + 2); 
    		$oiseau = $em->getRepository('AppBundle:OiseauTaxref')->findOneBy([
    			'nomComplet' => $nomOiseauComplet
    			]);
    		$observation->setOiseau($oiseau);

            // Si l'utilisateur est au moins naturaliste, son observation est validée tout de suite
            $isNaturaliste = $this->get('security.authorization_checker')->isGranted('ROLE_NATURALISTE');
            if ($isNaturaliste) {
                $observation->setStatus(Observation::VALIDEE);
                $observation->setPublish(new \DateTime('now'));

                $this->addFlash('notice', 'Merci d\'avoir soumis une observation, celle-ci vient d\'être publiée');
            }

    		$em->persist($observation);
    		$em->flush();

            if (!$isNaturaliste)
    		  $this->addFlash('notice', 'Merci d\'avoir soumis une observation, celle-ci va être validée par un professionnel avant d\'être publiée');
    		
            return $this->redirectToRoute('homepage');
    	}

    	return $this->render('front/observer.html.twig', [
    		'form' => $form->createView(),
    		'listOiseauNames' => $listOiseauNames
    		]);
    }
}
