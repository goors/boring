<?php

namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmeAccountBundle:Default:index.html.twig', array('name' => $name));
    }
    
    
    
    public function createAction(Request $request)
{
    
}
}
