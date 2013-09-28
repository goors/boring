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
use Mapado\MysqlDoctrineFunctions\MysqlDateFormat;

use Symfony\Component\HttpFoundation\Response;

class GiftController extends Controller {
    
    
    
    public function sendGiftAction(Request $request){
        
        
        /*
         * get new gifts
         */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(p.id)
            FROM AcmeAccountBundle:UserGift p WHERE p.new='1' AND p.received_by='".$this->getUser()->getId()."'
            "
        );

        $new_count = $query->getSingleScalarResult();
        
        
        
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
            
            
            //var_dump($this->getUser()->getId());
            
            $user_id = $request->get("name");
            $gift_id = $request->get('gift');
            
            $comment = $request->get('comment');
            
            
            
            
            $gift = $em->getRepository("Acme\AccountBundle\Entity\Gift")->find($gift_id);
            $sent_by = $em->getRepository("Acme\AccountBundle\Entity\User")->find($this->getUser()->getId());
            $received_by = $em->getRepository("Acme\AccountBundle\Entity\User")->find($user_id);
            
            $send_gift = new \Acme\AccountBundle\Entity\UserGift;
            $send_gift->setSentDate(new \DateTime(date("Y-m-d H:i:s")));
            $send_gift->setComment($comment);
            $send_gift->setGift($gift);
            $send_gift->setSentBy($sent_by);
            $send_gift->setReceivedBy($received_by);
            $send_gift->addUser($received_by);
            $send_gift->setNew(1);
            $em->persist($send_gift);
            $em->flush();
            
            
            
            
            $message = \Swift_Message::newInstance()
            ->setSubject('new Gift on Giftapp')
            ->setFrom('gift@pregmatch.org')
            ->setTo($received_by->getusername())
            ->setBody(
                
                "Hello, ".$received_by->getFirstName().". <br /><br />
                    You got ".$gift->getName()."from ".$sent_by->getFirstName(), 'text/html'    
            );
            $this->get('mailer')->send($message);
            return $this->render(
            'AcmeAccountBundle::gift_sent.html.twig',
                array('img' => true, 'message'=>'New gift is on the way.',"new_count"=>$new_count)
            );
            
        }
        
        
        
        return $this->render(
            'AcmeAccountBundle::sendgift.html.twig',
                array('users' => $users, 'gifts'=>$gifts,"new_count"=>$new_count)
            );
    }
    
    
    public function userStatsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository("Acme\AccountBundle\Entity\User")->findOneBy(array("first_name"=>$request->get('user')));
        
        
        /*
         * get total gifts for user
         */
        $query = $em->createQuery(
            "SELECT COUNT(p.received_by)
            FROM AcmeAccountBundle:UserGift p
            WHERE p.received_by = '".$user->getId()."' "
        );

        $totalgifts = $query->getSingleResult();
        
       
        
        
        
        
        /*
         * get most popular gift for user
         */
        
        $query = $em->createQuery(
            "SELECT p, COUNT(p.gift) as cnt
            FROM AcmeAccountBundle:UserGift p
            WHERE p.received_by = '".$user->getId()."' GROUP BY p.gift ORDER BY cnt DESC "
        );

        $bestgift = $query->getResult();
        
        $count = array();
        
        foreach($bestgift as $gift){
            array_push($count, array($gift[0]->getGift()->getId()=>$gift['cnt']));
        }
        
        $bestgift = array_search(max($count[0]), $count[0]);
        
        $bestgift = $em->getRepository("Acme\AccountBundle\Entity\Gift")->find($bestgift);
        
        foreach($count[0] as $time){
            $time = $time;
        }
        
        /*
         * get when user was most popular
         */
        
        $query = $em->createQuery(
                
            "SELECT COUNT( p.id ) AS counta, DATE_FORMAT( p.sent_date,  '%Y-%m' ) AS _month
            FROM AcmeAccountBundle:UserGift p
            WHERE p.received_by = '".$user->getId()."'
            GROUP BY _month
            ORDER BY _month
               "
                
                
                
            
        );
        
        $mostpopular = $query->getResult();
        
        /*
         * get all user gifts
         */
        
        
        $all_user_gifts = $em->getRepository("Acme\AccountBundle\Entity\UserGift")->findBy(array("received_by"=>$user));
        
        
        /*
         * get new gifts
         */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(p.id)
            FROM AcmeAccountBundle:UserGift p WHERE p.new='1' AND p.received_by='".$this->getUser()->getId()."'
            "
        );

        $new_count = $query->getSingleScalarResult();
        
        
        return $this->render(
            'AcmeAccountBundle::stats.html.twig',
                array(
                        "user"=>$user, 
                        "totalgifts"=>$totalgifts[1], 
                        "bestgift"=>$bestgift->getName(),
                        "best_gifts_total"=>$time, 
                        "mostpopular"=>$mostpopular[0]['_month'],
                        "all_user_gifts"=>$all_user_gifts,
                        "new_count"=>$new_count
                    )
            );
    }
    
    public function markAsReadAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $gift = $em->getRepository("Acme\AccountBundle\Entity\UserGift")->find($request->get('giftid'));
        $gift->setNew(0);
        
        
        $em->persist($gift);
        $em->flush();
        
        $data = array("result"=>1);
        
        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        
    }
}

?>
