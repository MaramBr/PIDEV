<?php

namespace App\Form;

use App\Entity\Coaching;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CoachingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCoach')
            ->add('prenomCoach')
            ->add('emailCoach')
            ->add('cours', ChoiceType::class, [
                'choices' => [
                    'Yoga' => 'Yoga',
                    'Fitness' => 'Fitness',
                    'Gymnas' => 'Gymnas',
                  
                ],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisissez une option',
            ])
            ->add('dispoCoach', ChoiceType::class, [
                'choices' => [
                    'En weekend' => 'weekend',
                    'Toujours' => 'toujours',
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'samedi',
                    'Dimanche' => 'dimanche',
                    'Autre' => 'Autre',
                ],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisissez une option',
            ])
            ->add('autre', TextType::class, [
                'label' => 'Autre',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
          
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary'],
            ])
           
            

            ->add('imgCoach', FileType::class, [
                'label' => 'image de coach (Image file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->getForm()
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaching::class,
        ]);
    }
}
