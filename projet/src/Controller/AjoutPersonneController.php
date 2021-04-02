<?php

namespace App\Controller;

use App\Form\AjoutPersonneType;
use App\Form\AjoutPersonneFileType;

use App\Entity\Etudiant;

use App\Service\Access;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Service\FileUploaderAdd;

/**
 * @IsGranted("ROLE_ADMINISTRATEUR")
 */
class AjoutPersonneController extends AbstractController
{
    /**
     * @Route("/ajout/personne", name="ajout_personne")
     */
    public function index(Request $request, FileUploaderAdd $fileUploader, UserPasswordEncoderInterface $encoder): Response
    {
        $a = new Access();
        $a->access();

        $form = $this->createForm(AjoutPersonneType::class);
        $form2 = $this->createForm(AjoutPersonneFileType::class);
        $form->handleRequest($request);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $etudiant = new Etudiant();
            $etudiant->setNomEtudiant($data['nom']);
            $etudiant->setPrenomEtudiant($data['prenom']);
            $etudiant->setNumEtudiant($data['netud']);
            $etudiant->setLogin(strtolower($data['prenom']) . '.' . strtolower($data['nom']));
            $hash = $encoder->encodePassword($etudiant, strtolower($data['nom'][0]) . $data['netud']);
            $etudiant->setPassword($hash);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('etudiant_index');
        } else if ($form2->isSubmitted() && $form2->isValid()) {
            $fichier = $form2['excel']->getData();
            if ($fichier) {
                $fileUploader->uploadAndRead($fichier);
            }
        }
        return $this->render('ajout_personne/ajouterEtudiant.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }
}
