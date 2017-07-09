<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 25/06/2017
 * Time: 12:17
 */

namespace AppBundle\Form;


use AppBundle\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{/**
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameIdentifier',     ChoiceType::class, array(
                    'choices'  => array(
                    'accueil' => 'accueil',
                    'mentions légales' => 'mentions-legales',
                    'about' =>'about'),
                'required'   =>  true ,
            ))
            ->add('title',      TextType::class, array(
                'required'   =>  true ,
            ))
            ->add('content',   TextareaType::class , array(
                'required'   =>  false ,
            ))
            ->add('enregistrer',      SubmitType::class);


        if ($options['data']->getNameIdentifier() == 'accueil') {
            $builder->add('photoBanner', FileType::class, array(
                'required'   =>  false ,
            ));
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}