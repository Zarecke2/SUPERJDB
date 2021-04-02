<?php

namespace App\Service;


use Symfony\Component\HttpFoundation\Session\Session;

class Administrateur
{

    public function isAdmin($value)
    {
        $session = new Session();
        if ($value == null) // Null pour l'obtenir
        {
            if ($session->get('mode_admin') == "yes")
            {
                return true;
            }
            else{
                return false;
            }
        }
        else if ($value == "no"){ // Mode admin : off
            $session->set('mode_admin', 'no');
            return false;
        }
        else if ($value === "yes"){ // Mode admin : on
            $session->set('mode_admin', 'yes');
            return true;
        }
    }
}