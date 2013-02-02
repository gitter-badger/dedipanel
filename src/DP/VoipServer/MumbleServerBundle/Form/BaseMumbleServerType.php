<?php

namespace DP\VoipServer\MumbleServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MumbleServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', 'entity', array(
                             'label' => 'voipserver.selectMachine', 
                             'class' => 'DPMachineBundle:Machine'))
            ->add('portMumble', 'number')
            ->add('iceSecret', 'password')
        ;
    }

    public function getName()
    {
        return 'dp_voipserver_mumbleserverbundle_mumbleservertype';
    }
}
