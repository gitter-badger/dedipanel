<?php

namespace DP\VoipServer\MumbleServerBundle\Form\Mumble;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MachineType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', 'entity', array(
                  'label' => 'game.selectMachine', 
                  'class' => 'DPMachineBundle:Machine'
                  ))
            ->add('icePort')
            ->add('iceSecret')
            ->add('query')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DP\VoipServer\MumbleServerBundle\Entity\Mumble\Machine'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dp_voipserver_mumbleserverbundle_mumble_machine';
    }
}
