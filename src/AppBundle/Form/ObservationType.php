<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ObservationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomOiseau', TextType::class, array(
                'required' => false))
            ->add('date', DateTimeType::class, array(
                'required' => false))
            ->add('photoFile', FileType::class, [
                'required' => false,
                'label'=> 'Photo',
                ])
            ->add('audioFile', FileType::class, [
                'required' => false,
                 'label'=> 'Audio',
                ])
            ->add('content',TextareaType::class,[
                'label'=> 'Remarque',
                'required' => false
                ])
            ->add('latitude', NumberType::class, array(
                'required' => false))
            ->add('longitude', NumberType::class, array(
                'required' => false))
            ->add('Soumettre', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Observation',
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_observation';
    }


}
