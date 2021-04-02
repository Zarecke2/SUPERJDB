<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AjoutPersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, array('label' => 'Nom :'))
            ->add('prenom', null, array('label' => 'Prénom :'))
            ->add('netud', IntegerType::class, array('label' => 'Numéro étudiant :',
            'constraints' =>[
                new Regex([ 'pattern' => '/^[0-9]{8}$/', 'message' => "La valeur doit correspondre à une numéro étudiant"]),
            ]
            
            
            
            ))
        ;
    }
}
