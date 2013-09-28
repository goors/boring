<?php
    // src/Acme/AccountBundle/Form/Type/UserType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        
        $email = (isset($_SESSION['reg_email']) ?$_SESSION['reg_email']: "" );
        $fname = (isset($_SESSION['reg_first_name']) ?$_SESSION['reg_first_name']: "" );
        $lname = (isset($_SESSION['reg_last_name']) ?$_SESSION['reg_last_name']: "" );
        
        
        $builder->add('first_name', 'text', array( 
            'attr'   =>  array(
                'value'=>$fname,
                'class'   => 'block input_block',
                'placeholder'=>"First name"
                ), 'label' => false,
            )
                 
        );
        $builder->add('last_name', 'text', array( 
            'attr'   =>  array(
                'value'=>$lname,
                'class'   => 'block input_block',
                'placeholder'=>"Last name"
                ), 'label' => false,
            )
                 
        );
        $builder->add('username', 'email', array( 
            'attr'   =>  array(
                'value'=> $email,
                'class'   => 'block input_block',
                'placeholder'=>"Your email"
                ), 'label' => false,
            )
                 
        );
        
        $builder->add('password', 'password', array( 
            'attr'   =>  array(
                'class'   => 'block input_block',
                )
            )
        );
        
        $builder->add('open_account', 'submit', array( 
            'attr'   =>  array(
                'class'   => 'block input_block button',
                )
            )
        );
        
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\AccountBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
?>