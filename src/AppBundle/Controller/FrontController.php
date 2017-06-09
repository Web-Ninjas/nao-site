<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
use AppBundle\Form\Model\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET","POST"})
     */
    public function indexAction(Request $request)
    {
            $page = $this->getDoctrine()->getRepository('AppBundle:Page')->find(1);

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

        $contact = new Contact();

        $form = $this->get('form.factory')->create(ContactType::class, $contact);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("NAO - Contact de :" . $contact->getNom() ." ".$contact->getPrenom())//Email devient le sujet de mon objet contact
                    ->setFrom($contact->getEmail())// Email de l'expéditeur est le destinataire du mail
                    ->setTo($this->getParameter('mailer_user'))// destinataire du mail
                    ->setBody("Message : " .$contact->getContenu()); // contenu du mail

                $this->get('mailer')->send($message);//Envoi mail

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

        $page = $this->getDoctrine()->getRepository('AppBundle:Page')->find(2);

        return $this->render('front/mentions.html.twig', array(
            'page' => $page,
        ));
    }

    /**
     * @Route("/actualites/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="actualites")
    */
    public function newsAction($page)
    {
    	// On cherche les 4 premiers articles pour les afficher
    	$em = $this->getDoctrine()->getManager();
    	$listArticles = $em->getRepository('AppBundle:Article')
    		->findBy(
    			array(),
    			array('date' => 'desc'),
    			4,
    			($page - 1) * 4
    		);

    	return $this->render('front/news.html.twig', array(
    		'listArticles' => $listArticles,
    		'page' => $page
    		));
    }
}
