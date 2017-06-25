<?php


namespace UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class ValidationMdp extends Constraint
{
    public $message = "Votre ancien mot de passe n'est pas correct !";

    private $passwordEncoder;


    public function validate()
    {


        return 'app_validationMdp'; // Ici, on fait appel à l'alias du service
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
