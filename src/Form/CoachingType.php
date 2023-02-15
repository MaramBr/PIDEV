<?php

namespace App\Form;

use App\Entity\Coaching;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
                    'Fitness' => 'Fit',
                    'Gymnass' => 'Gym'
                ]
            ])
            ->add('dispoCoach')
            ->add('imgCoach')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaching::class,
        ]);
    }
}
