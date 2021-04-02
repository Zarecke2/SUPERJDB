<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AjoutPersonneFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('excel', FileType::class, array('label' => 'Fichier : ', 'help' => 'Attention, votre fichier doit être en UTF-8 et 
                                                                                    doit contenir sur la première ligne l\'entête suivante 
                                                                                    dans l\'ordre : "nom" "prenom" et "netud".'))
        ;
    }
}
