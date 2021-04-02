<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MdpChange extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nouveau', PasswordType::class, ['label' => "Nouveau mot de passe : ",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Il faut rentrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],            
            ])
            ->add('confirmer', PasswordType::class, ['label' => "Confirmez : ",
            'constraints' => [
                new NotBlank([
                    'message' => 'Il faut rentrer un mot de passe',
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                    'max' => 4096,
                ]),
            ],            
        ])
        ;
    }
}

