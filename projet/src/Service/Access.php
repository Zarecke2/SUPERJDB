<?php

namespace App\Service;

use App\Service\Administrateur;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;


class Access
{
    public function access()
    {
        $admin = new Administrateur();
        if ($admin->isAdmin(null) == false) {
            throw new AccessDeniedException();
        }
    }
}
