<?php

namespace DP\VoipServer\MumbleServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MumbleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', 'entity', array(
                    'label' => 'mumble.selectMachine',
                    'class' => 'DPMachineBundle:Machine'))
            ->add('portIce', 'integer', array(
                    'label' => 'mumble.portIce'))
            ->add('iceSecret', 'text', array(
                    'label' => 'mumble.iceSecret'))
        ;
    }

    public function getName()
    {
        return 'dp_voipserver_mumbleserverbundle_mumbleservertype';
    }
}