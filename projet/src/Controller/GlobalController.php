<?php

namespace App\Controller;


use App\Entity\Etudiant;
use App\Entity\Enseignant;

use App\Service\Access;
use App\Form\MdpChange;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Service\Administrateur;

class GlobalController extends AbstractController
{
    private $user;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->admin = new Administrateur();
        $this->user = $tokenStorage->getToken()->getUser();
    }
    /**
     * @Route("/", name="redirect")
     *  
     */
    public function accueil(): Response
    {
        return $this->redirectToRoute('login');;
    }

    /**
     * @Route("/admin", name="administrateur")
     * 
     * @IsGranted("ROLE_ADMINISTRATEUR")
     */
    public function index(): Response
    {
        $value = $this->admin->isAdmin(null);
        if ($value == false) {
            return $this->redirectToRoute('journal_de_bord_index');
        }
        return $this->redirectToRoute('etudiant_index');;
    }

    /**
     * @Route("/nepasfairecelien", name="connected")
     */
    public function connected(): Response
    {
        $array = $this->user->getRoles();
        if (in_array('ROLE_ETUDIANT', $array)) {
            return $this->redirectToRoute('journal_de_bord_show');
        } else if (in_array('ROLE_ENSEIGNANT', $array)) {
            return $this->redirectToRoute('journal_de_bord_index');
        }
    }

    /**
     * @Route("/goAdmin", name="going_admin")
     * 
     * @IsGranted("ROLE_ADMINISTRATEUR")
     */
    public function goAdmin(): Response
    {
        $value = $this->admin->isAdmin(null);
        if ($value == "true") {
            $this->admin->isAdmin("no");
        } else {
            $this->admin->isAdmin("yes");
        }
        return $this->redirectToRoute('administrateur');
    }

    /**
     * @Route("/profil", name="profil")
     * 
     */
    public function profil(): Response
    {
        return $this->render('profil/show.html.twig', []);
    }


    /**
     * @Route("/mot_de_passe/", name="mdp", methods={"GET", "POST"})
     * 
     */
    public function mdp(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if (isset($_GET['id']) && isset($_GET['nom'])) {
            $repoEt = $this->getDoctrine()->getRepository(Etudiant::class);
            $user = $repoEt->findOneByIdAndName($_GET['id'], $_GET['nom']);
            $var = "et";
            if ($user == null) {
                $repoEn = $this->getDoctrine()->getRepository(Enseignant::class);
                $user = $repoEn->findOneByIdAndName($_GET['id'], $_GET['nom']);
                $var = "en";
            }
            else{
                $user[0] = $this->user;
            }
        }
        else{
            $user[0] = $this->user;
        }

        $form = $this->createForm(MdpChange::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (($data['nouveau'] != $data['confirmer'])) {
                $flashbag = $this->get('session')->getFlashBag();
                $flashbag->add("mdp", 'Attention les deux mots de passes ne correspondent pas !');
                return$this->redirectToRoute('mdp');
            }

            $hash = $encoder->encodePassword($user[0], $data['nouveau']);
            $user[0]->setPassword($hash);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user[0]);
            $entityManager->flush();
            if ($var == "en"){
                return $this->redirectToRoute('enseignant_index');
            } else if($var == 'et'){
                return $this->redirectToRoute('etudiant_index');
            }
            return $this->render('profil/show.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->render('profil/mdp.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
