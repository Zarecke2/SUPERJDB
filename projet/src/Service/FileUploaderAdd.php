<?php

namespace App\Service;

use App\Entity\Etudiant;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FileUploaderAdd
{
    private $targetDirectory;

    public function __construct($targetDirectory, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->targetDirectory = $targetDirectory;
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function uploadAndRead(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $originalFilename;
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $fichier = fopen($this->getTargetDirectory().$fileName, 'r');
        if (!$fichier)
            return FALSE;

        $verification = 1;
        $entityManager = $this->em;
        $etudiantRepo = $entityManager->getRepository(Etudiant::class);
        $allNum = $etudiantRepo->FindAllNetud();
        while(($buffer = fgetcsv($fichier, 4096)) != false){
            $buffer[0] = mb_convert_encoding($buffer[0], 'UTF-8' );
            $buffer[0] = str_replace("é", "e", $buffer[0]);
            if ($verification == 1)
            {
                $buffer[0] = strtolower($buffer[0]);
                $part = explode(";", $buffer[0]); 
                if ((strcmp($part[0], "nom")) || (strcmp($part[1], "prenom")))
                {
                    return FALSE;
                }
                $verification = 0;
                continue;
            }
            $part = explode(";", $buffer[0]);
            if (in_array($part[2], $allNum)){
                continue;
            }
                
            $etudiant = new Etudiant();
            $etudiant->setNomEtudiant($part[0]);
            $etudiant->setPrenomEtudiant($part[1]);
            $etudiant->setNumEtudiant($part[2]);
            $etudiant->setLogin(strtolower($part[1]).'.'.strtolower($part[0]));
            $hash = $this->encoder->encodePassword($etudiant, strtolower($part[0][0]).$part[2]);
            $etudiant->setPassword($hash);
            $entityManager->persist($etudiant);
        }
        $entityManager->flush();

        $filesystem = new Filesystem();
        $filesystem->remove($this->getTargetDirectory().$fichier);

        return TRUE;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}