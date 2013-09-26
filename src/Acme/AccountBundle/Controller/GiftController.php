<?php

namespace Acme\AccountBundle\Controller;

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Form\Type\LoginType;
use Acme\AccountBundle\Form\Model\Login;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class GiftController extends Controller {
    
    
    
    public function sendGiftAction(Request $request){
        
        
        
        
        
        
        /*
         * get EM
         */
        $em = $this->getDoctrine()->getManager();
        
        /*
         * get all users
         */
        
        $users = $em->getRepository("Acme\AccountBundle\Entity\User")->findAll();
        
        
        /*
         * get all gifts
         */
        
        $gifts = $em->getRepository("Acme\AccountBundle\Entity\Gift")->findAll();
        
        
        if($request->isMethod("POST")){
            
            $user_id = $request->get("name");
            $gift_id = $request->get('gift');
            
            $comment = $request->get('comment');
            
        }
        
        
        return $this->render(
            'AcmeAccountBundle::sendgift.html.twig',
                array('users' => $users, 'gifts'=>$gifts)
            );
    }
    
}

?>
