<?php
    // src/Acme/AccountBundle/Form/Type/UserType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        
        
        
        $builder->add('first_name', 'text', array( 
            'attr'   =>  array(
                
                'class'   => 'block input_block',
                'placeholder'=>"First name"
                ), 'label' => false,
            )
                 
        );
        $builder->add('last_name', 'text', array( 
            'attr'   =>  array(
                'class'   => 'block input_block',
                'placeholder'=>"Last name"
                ), 'label' => false,
            )
                 
        );
        $builder->add('username', 'email', array( 
            'attr'   =>  array(
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
        
        $builder->add('save_changes', 'submit', array( 
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