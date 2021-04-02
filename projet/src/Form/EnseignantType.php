<?php

namespace App\Form;

use App\Entity\Enseignant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_enseignant', null, array('label' => "Nom de l'enseignant : "))
            ->add('prenom_enseignant', null, array('label' => "Prénom de l'enseignant : "))
            ->add('administrateur', null, array('label' => "Administrateur ? "))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
