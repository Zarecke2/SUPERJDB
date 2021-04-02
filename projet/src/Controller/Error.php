<?php

namespace App\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;    
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Error extends AbstractController {

    protected $debug; // this is passed as a parameter from services.yaml
    protected $code;  // 404, 500, etc.
    protected $data;

    

    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null) {
        dd($exception); // uncomment me to see the exception

        $template = 'error' . $exception-> getStatusCode() . '.html.twig';
        return new Response($this->renderView($template, $this->data));
    }
} 