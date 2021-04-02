<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;


class NewPostType extends AbstractType
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['data'])) {
            $data = $options['data'];
            $id = $data['id'];
            $repo = $data['repo'];
            $result = $repo->findTitre($id);
            $titre = $result[0]['titre'];
            $texte = $result[0]['texte'];
        } else {
            $titre = null;
            $texte = null;
        }

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Nom du post : ',
                'data' => $titre,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre titre doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu : ',
                'data' => $texte, 'constraints' => [
                    new Length([
                        'min' => 50,
                        'minMessage' => 'Votre contenu doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ]
            ])

            ->add('fichier', FileType::class, [
                'label' => 'Ajouter un fichier* : ',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Vous devez mettre un pdf !',
                        
                    ])
                ]

            ]);
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
