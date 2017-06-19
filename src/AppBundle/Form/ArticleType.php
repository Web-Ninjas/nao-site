<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 19/06/2017
 * Time: 13:08
 */

namespace AppBundle\Form;


use AppBundle\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',     TextType::class)
            ->add('content',   TextareaType::class, array('required' => false, 'attr' => (array('class'=> 'ckeditor'))))
            ->add('enregistrer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}