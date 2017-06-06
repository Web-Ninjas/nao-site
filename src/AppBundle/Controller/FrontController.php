<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
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
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @Method({"GET","POST"})
     */
    public function ContactAction(Request $request)
    {

        $form = $this->get('form.factory')->create(ContactType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')//Message en HTML
                    ->setSubject("NAO - Contact de :" . $contact->getEmail())//Email devient le sujet de mon objet contact
                    ->setFrom($this->getParameter('mailer_user'))// Email de l'expéditeur est le destinataire du mail
                    ->setTo($this->getParameter('mailer_user'))// destinataire du mail
                    ->setBody($contact->getContenu()); // contenu du mail

                $this->get('mailer')->send($message);//Envoi mail

                $this->addFlash('notice', 'Votre message a bien été envoyé !');

                return $this->redirectToRoute('front/index.html.twig');
            }
        }

        return $this->render('front/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
