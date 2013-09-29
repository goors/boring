<?php
    // src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Type\AccountType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Form\Model\Account;
use Acme\AccountBundle\Form\Type\LoginType;
use Acme\AccountBundle\Form\Model\Login;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Facebook;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


//use Symfony\Component\Security\Core\A

class AccountController extends Controller
{
    
    
    


    public function indexAction(Request $request){
        
        if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect("/");
        }
            
            
         /*
         * get last 12 gifts and sent to view
         */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
            FROM AcmeAccountBundle:UserGift p
            ORDER BY p.sent_date ASC'
        );

        $gifts = $query->getResult();
        
        
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
            'AcmeAccountBundle::home.html.twig',
                array(
                       'user' => $this->getUser(), 
                       'message'=>false, 
                       'gifts'=>$gifts,
                       'new_count'=>$new_count 
                     )
            );
            
    }

        public function registerAction()
    {
            
            
            
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'AcmeAccountBundle::layout.html.twig',
            array('form' => $form->createView(), 'message'=>false)
        );
    }
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());
        $form->handleRequest($request);
    
        
        if ($form->isValid()) {
            $registration = $form->getData();
            
            
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($registration->getUser());
            $salt = md5($registration->getUser()->getUsername().time());
            $password = $encoder->encodePassword($registration->getUser()->getPassword(), $salt);
            $registration->getUser()->setPassword($password);
            $registration->getUser()->setSalt($salt);
            
            $em->persist($registration->getUser());
            $em->flush();


            $message = \Swift_Message::newInstance()
            ->setSubject('Activation email')
            ->setFrom('activation@pregmatch.org')
            ->setTo($registration->getUser()->getUsername())
            ->setBody(
                
                "Hello, ".$registration->getUser()->getFirstName().". <br /><br />
                    Please click on <a href=\"http://".$request->server->get('HTTP_HOST')."/activation/email/".$registration->getUser()->getUsername()."/code/".$registration->getUser()->getAcc()."\">this link</a> to activate your account on Gift app.", 'text/html'    
            )
        ;
        $this->get('mailer')->send($message);
        
        }
   
        return $this->render(
            'AcmeAccountBundle::message.html.twig',
            array("message"=>"Please check your email for activation email.","img"=>"")
        );
    }
    
    public function activateAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("Acme\AccountBundle\Entity\User")->findOneBy(array("username"=>$request->get("email"), "acc"=>$request->get("code")));
        $role = $em->getRepository("Acme\AccountBundle\Entity\Role")->findOneBy(array("role"=>"ROLE_USER"));
        
        if(count($user->getUsername()) && $user->getIsActive() == 0){
            $user->setIsActive(1);
            $user->addRole($role);
            $em->persist($user);
            $em->flush();
            
            
            
            //$user->setRole($em->getR)
            
            return $this->render(
                'AcmeAccountBundle::message.html.twig',
                array("message"=>"Your account is activated. You can no login with your email.","loggedin"=>false,"img"=>"")
            );
        }
        else{
            return $this->render(
            'AcmeAccountBundle::message.html.twig',
            array("message"=>"Wrong email or activation code.","loggedin"=>false,"img"=>"")
        );
        }
    }
    
    public function loginAction(Request $request){
        
        
        if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect("/home");
        }
        
        
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        //var_dump($error);
        return $this->render('AcmeAccountBundle::login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
        
        
    }
    
    public function logoutAction(Request $request){
        $session = $request->getSession();
        $session->clear();
        return $this->redirect("/login");
       
    }
    
    
    public function facebookLoginAction(Request $request){
        
        
        $request = $this->getRequest(); 
        $session = $request->getSession();
        
        $app_id = $this->container->getParameter('facebook.credentials.app_id'); 
        $app_secret = $this->container->getParameter('facebook.credentials.app_secret'); 
        
        
        
        $facebook = new Facebook( array( 'appId' => $app_id, 'secret' => $app_secret, 'cookie' => false, )); 
        
        
        if($facebook->getUser()){
            $me = $facebook->api('/me');
            
            
                $em = $this->getDoctrine()->getManager();
                
                $user = $em->getRepository("Acme\AccountBundle\Entity\User")->findOneBy(array("username"=>$me['email']));
                
                $email = $me['email'];
                if($user){
                    
                     $firewall = "main"; 
                     $roles = $user->getRoles(); 
                     $rolesArray = array(); 
                     foreach($roles as $role): 
                         $rolesArray[] = $role->getRole(); 
                     endforeach; 
                     
                     $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $rolesArray); 
                     $event = new InteractiveLoginEvent($this->getRequest(), $token); 
                     $this->get("event_dispatcher")->dispatch("security.interactive_login", $event); 
                     $this->get('security.context')->setToken($token); 
                     var_dump($roles);
                     return $this->redirect("/home");
                }
                    
                    
                
                else{
                    
                    $_SESSION['reg_email'] = $email;
                    $_SESSION['reg_first_name'] = $me['first_name'];
                    $_SESSION['reg_last_name'] = $me['last_name'];
                    return $this->redirect("/register");
                }
        }
        else{
            $loginUrl = $facebook->getLoginUrl(array('scope' => 'email,publish_stream,status_update,offline_access'));
            
            return $this->redirect($loginUrl);
        }
        
        
        
        
          
         
         
        return $this->render(
                    'AcmeAccountBundle::login.html.twig',
                    array('error'=>"", 'message'=>"", )
                );
    }
    
    
    
    public function accountEditAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository("Acme\AccountBundle\Entity\User")->find($this->getUser()->getId());
        
        $form = $this->createForm(new AccountType(), $user, array(
            'action' => $this->generateUrl('account_edit'),
        ));
        
        
        
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
        
        $request = $this->getRequest();
        
        if ( $request->getMethod() == 'POST' ) {
            
            $form->bind( $request );
            
            if ( $form->isValid() ) {
                
                $params = $this->getRequest()->request->all();
                print_r($params);
                
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user->getUser());
                $password = $encoder->encodePassword($params["account"]["user"]["password"], $user->getSalt());
                $user->setPassword($password);
                
                
                $em->persist( $user );
                $em->flush();
                
                
                
                
                return $this->render(
                'AcmeAccountBundle::account.html.twig',
                    array(
                           'form' => $form->createView(), 
                           'message'=>"Your account was updated.", 
                           'new_count'=>$new_count 
                         )
                );
            }
        }
        
        
        

        
        
        
        return $this->render(
            'AcmeAccountBundle::account.html.twig',
                array(
                       'form' => $form->createView(), 
                       'message'=>false, 
                       'new_count'=>$new_count 
                     )
            );
        
        
        
    }
}
?>