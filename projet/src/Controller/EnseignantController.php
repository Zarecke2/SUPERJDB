<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Form\EnseignantType;

use App\Service\Access;

use App\Repository\EnseignantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/enseignant")
 * 
 * @IsGranted("ROLE_ADMINISTRATEUR")
 */
class EnseignantController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }
    
    /**
     * @Route("/", name="enseignant_index", methods={"GET"})
     */
    public function index(EnseignantRepository $enseignantRepository): Response
    {
        $a = new Access();
        $a->access();
        return $this->render('enseignant/index.html.twig', [
            'enseignants' => $enseignantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau", name="enseignant_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {   
        $a = new Access();
        $a->access();

        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $enseignant->setLogin(strtolower($enseignant->getPrenomEnseignant()).'.'.strtolower($enseignant->getNomEnseignant()));
            $hash = $encoder->encodePassword($enseignant, (strtolower($enseignant->getPrenomEnseignant()).strtolower($enseignant->getNomEnseignant())));
            $enseignant->setPassword($hash);
            $entityManager->persist($enseignant);
            $entityManager->flush();

            return $this->redirectToRoute('enseignant_index');
        }

        return $this->render('enseignant/new.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="enseignant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Enseignant $enseignant): Response
    {
        $a = new Access();
        $a->access();
        
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('enseignant_index');
        }

        return $this->render('enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enseignant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Enseignant $enseignant): Response
    {
        $a = new Access();
        $a->access();

        $jdb = $enseignant->getEquipeEnseignant();
        if ($jdb != null){
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->add("impossible", 'Il faut d\'abord enlever l\'enseignant de ses journaux de bords !'); 
            return $this->redirectToRoute('enseignant_index');
        }

        if ($this->isCsrfTokenValid('delete'.$enseignant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($enseignant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('enseignant_index');
    }
}
