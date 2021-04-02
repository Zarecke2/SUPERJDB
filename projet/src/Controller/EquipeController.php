<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\JournalDeBord;
use App\Service\Access;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/equipe")
 * 
 * @IsGranted("ROLE_ADMINISTRATEUR")
 */
class EquipeController extends AbstractController
{

    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @Route("/", name="equipe_index", methods={"GET"})
     */
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouvelle_equipe", name="equipe_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $a = new Access();
        $a->access();

        $equipe = new Equipe();
        $jdb = new JournalDeBord();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getdata();
            $etudiants = $equipe->getEtudiantEquipe();

            $message = "Les étudiants suivant :";

            $flag = 0;

            foreach ($etudiants as $etudiant) {
                if ($etudiant->getEquipeEtudiant() == null) {
                    $etudiant->setEquipeEtudiant($equipe);
                    $etudiant->setEnseignantEtudiant($equipe->getEnseignantEquipe());

                    $entityManager->persist($etudiant);
                } else {
                    $message .= $etudiant; // Ajout des étudiants avec une équipe
                    $flag = 1;
                    continue;
                }
            }
            if ($flag == 1) { // Cette partie sert à écrire le message pour qu'il puisse ensuite être affiché
                $message .= " ont déjà une équipe et n'ont pas été ajouté.";
                $flashbag = $this->get('session')->getFlashBag();
                $flashbag->add("doublon", $message);
            }
            $entityManager->persist($equipe); // Equipe créée
            $jdb->setNomJournal($equipe->getNomEquipe()); // JDB créé
            $jdb->setEquipeJdb($equipe);
            $jdb->setLu(false);
            $entityManager->persist($jdb);

            $entityManager->flush(); // Entitée enregistée

            return $this->redirectToRoute('equipe_index');
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipe_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        $a = new Access();
        $a->access();

        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="equipe_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Equipe $equipe): Response
    {
        $a = new Access();
        $a->access();

        $form = $this->createForm(EquipeType::class, $equipe);
        $etudiants = $equipe->getEtudiantEquipe();
        if ($etudiants != null) {
            $i = 0;
            foreach ($etudiants as $value) {
                $ancien[$i] = $value;
                $i++;
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getdata();
            $nouveau = $equipe->getEtudiantEquipe();

            if (isset($ancien)) {
                foreach ($nouveau as $value) { // Boucle des nouveaux
                    foreach ($ancien as $value2) { //Boucle des anciens
                        if ($value == $value2) // L'étudiant reste
                        {
                            $flag = 0;
                            break;
                        } else { // L'étudiant ne correspond pas
                            $flag = 1;
                        }
                    }
                    if ($flag == 1) // Pas de correspondance donc nouveau étudiant
                    {
                        $value->setEquipeEtudiant($equipe);
                        $value->setEnseignantEtudiant($equipe->getEnseignantEquipe());
                        $entityManager->persist($value);
                    }
                }
                foreach ($ancien as $value) { // Boucle des anciens
                    foreach ($nouveau as $value2) { //Boucle des nouveux
                        if ($value == $value2) // L'étudiant reste
                        {
                            $flag = 0;
                            break;
                        } else { // L'étudiant ne correspond pas et n'est plus dans l'équipe
                            $flag = 1;
                        }
                    }
                    if ($flag == 1) // Pas de correspondance donc l'étudiant n'est plus dans l'équipe
                    {
                        $value->setEnseignantEtudiant(null);
                        $value->setEquipeEtudiant(null);
                        $entityManager->persist($value);
                    }
                }
            } else {
                foreach ($etudiants as $nouveau) {
                    if ($nouveau->getEquipeEtudiant() == null) {
                        $nouveau->setEquipeEtudiant($equipe);
                        $entityManager->persist($nouveau);
                    } else
                        continue;
                }
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('equipe_index');
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipe_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Equipe $equipe): Response
    {
        $a = new Access();
        $a->access();
        
        if ($this->isCsrfTokenValid('delete' . $equipe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $jdb = new JournalDeBord();
            $jdb = $equipe->getJdbEquipe();
            $posts = $jdb->getPostJdb();
            $etudiants = $equipe->getEtudiantEquipe();
            foreach($etudiants as $etudiant){
                $etudiant->setEquipeEtudiant(null);
            }
            foreach ($posts as $post){

                $doc = $post->getDocumentPost();
                if ($doc != null){
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getTargetDirectory().$doc->getNom());
                    $entityManager->remove($doc);
                }

                $commentaire = $post->getCommentaire();
                if ($commentaire != null){
                    for ($i = 0; $i < size($commentaire); $i++){
                        $entityManager->remove($commentaire[$i]);
                    }
                }
                $entityManager->remove($post);
            }
            $entityManager->remove($jdb);
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipe_index');
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
