<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;

class TexteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commentaire', TextareaType::class, ['label' => false,
            'constraints' => [
                new Length([
                    'min' => 10,
                    'minMessage' => 'Votre commentaire doit faire au moins {{ limit }} caractères.',
                    'max' => 4096,
                ]),
            ]
            ])
        ;
    }
}

