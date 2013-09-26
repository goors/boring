<?php
    // src/Acme/AccountBundle/Form/Type/RegistrationType.php
namespace Acme\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType(),array('label'=>false));
        $builder->add(
            'i_accept_terms_of_use',
            'checkbox',
            array('property_path' => 'termsAccepted', 'attr'=>array("class"=>"checkbox"))
        );
    }

    public function getName()
    {
        return 'registration';
    }
}
?>