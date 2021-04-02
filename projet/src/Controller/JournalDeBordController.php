<?php

namespace App\Controller;

use App\Entity\JournalDeBord;

use App\Repository\JournalDeBordRepository;
use App\Entity\Post;
use App\Repository\EtudiantRepository;
use App\Repository\PostRepository;

use App\Service\Administrateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/journal/de/bord")
 */
class JournalDeBordController extends AbstractController
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }
    /**
     * @Route("/", name="journal_de_bord_index", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_ADMINISTRATEUR') or is_granted('ROLE_ENSEIGNANT')")
     */
    public function index(JournalDeBordRepository $journalDeBordRepository, Administrateur $admin): Response
    {
        $array = $this->user->getRoles();
        
        if (in_array('ROLE_ENSEIGNANT', $array)) {
            if ($admin->isAdmin(null)) { // ADMIN
                return $this->render('journal_de_bord/index.html.twig', [
                    'journal_de_bords' => $journalDeBordRepository->findAll(),
                ]);
            } else { //ENSEIGNANT
                $equipes = $this->user->getEquipeEnseignant();
                $i = 0;
                foreach ($equipes as $equipe) {
                    $jdb[$i] = $equipe->getJdbEquipe();
                    $i++;
                }
                return $this->render('journal_de_bord/index.html.twig', [
                    'journal_de_bords' => $jdb,
                ]);
            }
        }
    }

    /**
     * @Route("/show", name="journal_de_bord_show", methods={"GET"})
     */
    public function show(JournalDeBordRepository $repo): Response
    {
        $admin = new Administrateur();
        $roles = $this->user->getRoles();
        if (in_array('ROLE_ETUDIANT', $roles)) {
            $equipe = $this->user->getEquipeEtudiant();
            $jdb = $equipe->getJdbEquipe();
            $posts = $jdb->getPostJdb();
            return $this->render('journal_de_bord/show.html.twig', [
                'journal_de_bord' => $jdb,
                'posts' => $this->getDoctrine()->getRepository(Post::class)->postJdbOrdered($jdb->getId()),
            ]);
        }
        elseif (in_array('ROLE_ENSEIGNANT', $roles) && ($admin->isAdmin(null) == false || $admin->isAdmin(null) == null)){
            $journalDeBord = $repo->findOneById($_GET['id']);
            $posts = $journalDeBord->getPostJdb();
            $flag = 0;
            foreach ($posts as $element){
                $lu = $element->getLu();
                if ($lu == false){
                    $flag = 1;
                    $journalDeBord->setLu(false);
                    break;                   
                }
            }
            if ($flag == 0 )
                $journalDeBord->setLu(true);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journalDeBord);
            $entityManager->flush();
            
            return $this->render('journal_de_bord/show.html.twig', [
                'journal_de_bord' => $journalDeBord,
                'posts' => $this->getDoctrine()->getRepository(Post::class)->postJdbOrdered($journalDeBord->getId()),
            ]);
        }
	$journalDeBord = $repo->findOneById($_GET['id']);
        return $this->render('journal_de_bord/show.html.twig', [
            'journal_de_bord' => $journalDeBord,
            'posts' => $this->getDoctrine()->getRepository(Post::class)->postJdbOrdered($journalDeBord->getId()),
        ]);
    }
}
