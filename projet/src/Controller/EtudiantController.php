<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Service\Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/etudiant")
 * 
 * @IsGranted("ROLE_ADMINISTRATEUR")
 */
class EtudiantController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->roles = $this->user->getRoles();
    }
    
    /**
     * @Route("/", name="etudiant_index", methods={"GET"})
     */
    public function index(EtudiantRepository $etudiantRepository): Response
    {
        $a = new Access();
        $a->access();
        return $this->render('etudiant/index.html.twig', [
            'etudiants' => $etudiantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="etudiant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etudiant $etudiant, UserPasswordEncoderInterface $encoder): Response
    {
        $a = new Access();
        $a->access();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
            $etudiant->setPassword($hash);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etudiant_index');
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etudiant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Etudiant $etudiant): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('etudiant_index');
    }

}
