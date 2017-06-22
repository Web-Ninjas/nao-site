<?php

namespace AppBundle\Manager;

use AppBundle\Form\Model\Contact;
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
     * Permet d'envoyer un e-mail de confirmation que la commande est bien passée.
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

}
