<?php

namespace App\Form;

use App\Entity\Transporteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransporteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('prix')
            ->add('description')
            ->add('image',  FileType::class, [
                'label' => 'image de Transporteur (des fichiers images uniquement)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                //'constraints' => [
                 //   new File([
                        
                  //      'mimeTypes' => [
                   //         'image/jpg',
                    //        'image/png',
                    //        'image/gif',
                      //  ],
                    //    'mimeTypesMessage' => 'Please upload a valid image',
                    //])
                //],
            ])
            ->add('nom')
            ->add('companies')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transporteur::class,
        ]);
    }
}
