<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Observation;
use AppBundle\Form\Model\Contact;
use Symfony\Bundle\TwigBundle\TwigEngine;

class MailContactManager
{
    /** @var TwigEngine */
    private $view;

    /** @var string */
    private $userEmail;

    /** @var \Swift_Mailer */
    private $mailer;

    public function __construct(TwigEngine $view, $userEmail, \Swift_Mailer $mailer)
    {
        $this->view = $view;
        $this->userEmail = $userEmail;
        $this->mailer = $mailer;
    }

    /**
     * Permet d'envoyer un e-mail.
     *
     * @param Contact $contact
     */
    public function envoyerMailContact(Contact $contact)
    {

        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')//Message en HTML
            ->setSubject('NAO - Contact')
            ->setFrom($contact->getEmail())// Email de l'expéditeur est le destinataire du mail
            ->setTo($this->userEmail)// destinataire du mail
            ->setBody($this->view->render('/mail/mailContact.html.twig', array('contact'=>$contact))); // contenu du mail

        $this->mailer->send($message);//Envoi mail
    }

    /**
     * Permet d'envoyer un e-mail pour le commentaire de non validation.
     *
     * @param Observation $observation
     */
    public function envoyerMailCommentaireNonValidation(Observation $observation)
    {

        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')//Message en HTML
            ->setSubject('Observation à modifier')
            ->setFrom($this->userEmail)// Email de l'expéditeur est le destinataire du mail
            ->setTo($observation->getAuthor()->getEmail())// destinataire du mail
            ->setBody($this->view->render('mail/mailNonValidationObservation.html.twig', array('observation'=>$observation))); // contenu du mail

        $this->mailer->send($message);//Envoi mail
    }

}
