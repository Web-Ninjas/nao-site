<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Observation;
use AppBundle\Form\Model\Contact;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\TwigBundle\TwigEngine;

class MailContactManager
{/** @var TwigEngine */
    private $view;

    /** @var string */
    private $from;

    /** @var \Swift_Mailer */
    private $mailer;

    public function __construct(TwigEngine $view, $from, \Swift_Mailer $mailer)
    {
        $this->view = $view;
        $this->from = $from;
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
            ->setTo('nao.site.w@gmail.com')// destinataire du mail
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
            ->setFrom('nao.site.w@gmail.com')// Email de l'expéditeur est le destinataire du mail
            ->setTo($observation->getAuthor()->getEmail())// destinataire du mail
            ->setBody($this->view->render('mail/mailNonValidationObservation.html.twig', array('observation'=>$observation))); // contenu du mail

        $this->mailer->send($message);//Envoi mail
    }

}
