<?php
    // src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Form\Type\LoginType;
use Acme\AccountBundle\Form\Model\Login;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class AccountController extends Controller
{
    
    public function indexAction(Request $request){
        
        //var_dump($this->getUser()->getEmail()); 
        
        //if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->render(
            'AcmeAccountBundle::home.html.twig',
                array('user' => $this->getUser(), 'message'=>false)
            );
        //}       
        //else{
          //  return $this->redirect("/");
        //}
            
         
            
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
            array("message"=>"Please check your email for activation email.")
        );
    }
    
    public function activateAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("Acme\AccountBundle\Entity\User")->findOneBy(array("username"=>$request->get("email"), "acc"=>$request->get("code")));
        $role = $em->getRepository("Acme\AccountBundle\Entity\Role")->findOneBy(array("role"=>"ROLE_USER"));
        
        if(count($user->getusername()) && $user->getIsActive() == 0){
            $user->setIsActive(1);
            $user->addRole($role);
            $em->persist($user);
            $em->flush();
            
            
            
            //$user->setRole($em->getR)
            
            return $this->render(
                'AcmeAccountBundle::message.html.twig',
                array("message"=>"Your account is activated. You can no login with your email.","loggedin"=>false)
            );
        }
        else{
            return $this->render(
            'AcmeAccountBundle::message.html.twig',
            array("message"=>"Wrong email or activation code.","loggedin"=>false)
        );
        }
    }
    
    public function loginAction(Request $request){
        
        
        
        
        
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
        
        
        /*$login = new Login();
        
        $form = $this->createForm(new LoginType(), $login, array(
            'action' => $this->generateUrl('login'),
        ));*/
            
        //$factory = $this->get('security.encoder_factory');
        //var_dump($factory);
        //$user = new User;
        //$encoder = $factory->getEncoder($user);
        //$password = $encoder->encodePassword('12345678', 'sanja11');
        //$user->setPassword($password);
               // echo $password;
        
        //$session = $request->getSession();
        //var_dump($request->getMethod())
        /* if($request->isMethod("POST")){
            var_dump($request->getMethod());
            //echo $password;
            $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
 
        //var_dump($error);
        return $this->render(
                'AcmeAccountBundle::login.html.twig',
                array('error'=>$error, "last_username"=>$session->get(SecurityContext::LAST_USERNAME))
            );
            
            /*$data = $request->request->all();
            
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("Acme\AccountBundle\Entity\User")->findOneBy(
                    array(
                        "email"=>$data['login']['user']['email'], 
                        "password"=>md5($data['login']['user']['password']),
                        "status"=>1
                        )
                    );
            
            if(count($user)){
                if($user->getEmail() && $user->getStatus() == 1){
                    
                    $session->clear();
                    
                    $session->set("login", $user);
                    
                    return $this->redirect("/home");
                    
                }
                else {
                    return $this->render(
                        'AcmeAccountBundle::login.html.twig',
                        array('form' => $form->createView(), 'message'=>"Account not activated.","loggedin"=>false)
                    );
                }
                
            }
            else{
                
                return $this->render(
                    'AcmeAccountBundle::login.html.twig',
                    array('form' => $form->createView(), 'message'=>"Credentials that you entered are wrong.")
                );
            }
            
            
        }
        else{
            
            return $this->render(
                'AcmeAccountBundle::login.html.twig',
                array('error'=>false)
            );
            
        }*/
    }
    
    public function logoutAction(Request $request){
        $session = $request->getSession();
        $session->clear();
        
       
    }
}
?>