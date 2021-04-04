<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\JournalDeBord;
use App\Entity\Post;
use App\Service\Access;
use App\Form\NewPostType;
use App\Form\TexteType;
use App\Repository\PostRepository;
use App\Service\Administrateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\WebpackEncoreBundle\Asset\IntegrityDataProviderInterface;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/nouveau_post/", name="post_new", methods={"GET", "POST"}, requirements={"id":"\d+"})
     * 
     * @IsGranted("ROLE_ETUDIANT")
     */
    public function new(Request $request): Response
    {
        $jdb = $this->user->getEquipeEtudiant()->getJdbEquipe();
        $post = new Post();
        $document = new Document();
        $form = $this->createForm(NewPostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $post->setNomPost($data['titre']);
            $texte = $data['contenu'];
            $texte = \str_replace("\r\n", "\n", $texte);
            $post->setTexte($texte);
            $post->setCommentaire(false);
            $post->setLu(false);
            $post->setDatePost(time());
            $post->setRelatedTo(null);
            $post->setJdbPost($jdb);
            $post->setPosteur($this->user);
            $jdb->setLu(false);

            $entityManager->persist($post);
            $entityManager->persist($jdb);

            if (isset($data['fichier'])) {
                $brochureFile = $data['fichier'];
            } else {
                $brochureFile = false;
            }


            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = ($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e;
			die;// ... handle exception if something happens during file upload
                }

                $document->setDateDepot(time())
                    ->setEtudiantDocument($this->user)
                    ->setNom($newFilename)
                    ->setPathDoc($this->getParameter('files_directory'))
                    ->setPostDocument($post)
                    ->setTypeDoc('pdf');

                $entityManager->persist($document);

                $post->setDocumentPost($document);
                $entityManager->persist($post);
            }
            $entityManager->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET", "POST"})
     */
    public function show(Post $post, Request $request): Response
    {
        $form = $this->createForm(TexteType::class);
        $form->handleRequest($request);
        $admin = new Administrateur();
        if (in_array("ROLE_ENSEIGNANT", $this->user->getRoles()) && ($admin->isAdmin(null) == false || $admin->isAdmin(null) == null)){
            $post->setLu(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }
        elseif ($admin->isAdmin(null) == true){
            $commentaire = $this->getDoctrine()->getRepository(Post::class)->findCommentaire($post->getId());
            return $this->render('post/show.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
                'commentaire' => $commentaire,
            ]);
        } else {
            $etudiants = $post->getJdbPost()->getEquipeJdb()->getEtudiantEquipe();
            $flag = 0;
            foreach ($etudiants as $etudiant){
                if ( $etudiant->getNomEtudiant() == $this->user->getNomEtudiant() ){
                    $flag = 1;
                } 
            }
            if ($flag == 0){
                $access = new Access();
                $access->access();
            }
            
        }
        

        if ($form->isSubmitted() && $form->isvalid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $jdb = $post->getJdbPost();

            $commentaire = new Post();
            $commentaire->setNomPost("commenaire");
            $commentaire->setTexte($form['commentaire']->getData());
            $commentaire->setCommentaire(true);
            $commentaire->setLu(true);
            $commentaire->setDatePost(time());
            $commentaire->setRelatedTo($post->getId());
            $commentaire->setJdbPost($jdb);
            $commentaire->setPosteur($this->user);

            if (in_array('ROLE_ETUDIANT', $this->user->getRoles())){
                $jdb->setLu(false);
                $post->setLu(false);
            }
            else{
                $jdb->setLu(true);
                $post->setLu(true);
            }
            $entityManager->persist($commentaire);
            $entityManager->flush();
        }
        $commentaire = $this->getDoctrine()->getRepository(Post::class)->findCommentaire($post->getId());
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="post_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     * 
     * @IsGranted("ROLE_ETUDIANT")
     */
    public function edit(Request $request, Post $post, PostRepository $repo): Response
    {
        $id = $post->getId();
        $form = $this->createForm(NewPostType::class, ['repo' => $repo, 'id' => $id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if (($form['fichier']->getData())) {
                $document = $post->getDocumentPost();
                if ($document) {// on supprime l'ancien
                    $filesystem = new Filesystem();
                    $filesystem->remove($document->getPathDoc() . $document->getNom());
                    
                } else { // on en crée un nouveau
                    $document = new Document();
                } 
                // on le met de nouveau dans la bdd et le filesystem
                $brochureFile = $form['fichier']->getData();
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = ($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                    try {
                        $brochureFile->move(
                            $this->getParameter('files_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $document->setDateDepot(time())
                        ->setEtudiantDocument($this->user)
                        ->setNom($newFilename)
                        ->setPathDoc($this->getParameter('files_directory'))
                        ->setPostDocument($post)
                        ->setTypeDoc('pdf');

                    $entityManager->persist($document);

                    $post->setDocumentPost($document);
                    $entityManager->persist($post);
                }
            }

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {
        $jdb = $post->getJdbPost();
        $commentaire = $this->getDoctrine()->getRepository(Post::class)->findCommentaire($post->getId());
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $commentaire = $this->getDoctrine()->getRepository(Post::class)->findCommentaire($post->getId());
            foreach($commentaire as $com){
                $entityManager->remove($com);
            }
            $entityManager->flush();
        }

        if(\in_array('ROLE_ETUDIANT', $this->user->getRoles()))
            return $this->redirectToRoute('journal_de_bord_show');
        else
            return $this->redirectToRoute('journal_de_bord_show', array('id' => $jdb->getId()));
    }
}
