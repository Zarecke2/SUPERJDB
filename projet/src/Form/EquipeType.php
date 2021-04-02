<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_equipe', null, array('label' => 'Nom de l\'équipe :  ', 'help' => 'Le nom du journal de bord sera celui de l\'équipe '))
            ->add('etudiantEquipe', null, array('label' => 'Étudians de l\'équipe :  '))
            ->add('enseignantEquipe', null, array('label' => 'Enseignant de l\'équipe :  '))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
