<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Session\Session;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    public function __construct(TokenStorageInterface $tokenStorage, HttpUtils $httpUtils)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = new Session();
        $this->httpUtils = $httpUtils;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        
        $roles = $this->tokenStorage->getToken()->getUser()->getRoles();      
        if (in_array('ROLE_ETUDIANT', $roles)){
            
            $this->session->getFlashBag()->add('access', 'Vous n\'êtes pas autorisez à accèder à cette page !');
            return $this->httpUtils->createRedirectResponse($request, 'journal_de_bord_show');
        }
        
        else if (in_array('ROLE_ENSEIGNANT', $roles))
        {
            $this->session->getFlashBag()->add('access', 'Vous n\'êtes pas autorisez à accèder à cette page !');
            return $this->httpUtils->createRedirectResponse($request, 'journal_de_bord_index');
        }

        return new Response($content, 403);
    }
}