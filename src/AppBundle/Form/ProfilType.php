<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class ProfilType extends AbstractType

{ /**
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',    TextType::class)
            ->add('firstName',    TextType::class)
            ->add('username', TextType::class)
            ->add('email',    EmailType::class, array('constraints' =>(array(new Email())))  )
            ->add('birthDate', BirthdayType::class)
            ;


        if (in_array('ROLE_NATURALISTE', $options['data']->getRoles())) {
            $builder->add('isContributeur', CheckboxType::class, [
                'mapped' => false,
                'required'=>false,
                'label' => 'Je veux être contributeur',
            ]);
        }


        if (in_array('ROLE_PARTICULIER', $options['data']->getRoles())) {
            $builder
                ->add('isNaturaliste', CheckboxType::class, [
                    'mapped' => false,
                    'required'=>false,
                    'label' => 'Je veux être naturaliste',
                    'data' => $options['data']->getDemandeNaturaliste() !== null
                ])
            ;
        }


        $builder
            ->add('nomEntreprise', TextType::class, array('required'=>false,))
            ->add('nSiret', TextType::class, array('required'=>false,))
        ;


        $builder->add('enregistrer',      SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'allow_extra_fields' => true,
        ));
    }

}