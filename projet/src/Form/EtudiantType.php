<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num_etudiant', null, array('label' => 'Numéro étudiant : '))
            ->add('nom_etudiant', null, array('label' => 'Nom : '))
            ->add('prenom_etudiant', null, array('label' => 'Prénom : ',
            'constraints' =>[
                new Regex([ 'pattern' => '/^[0-9]{8}$/', 'message' => "La valeur doit correspondre à une numéro étudiant"]),
            ]))            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
