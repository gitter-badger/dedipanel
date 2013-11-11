<?php

namespace DP\VoipServer\MumbleServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MumbleType extends AbstractType
{
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DP\VoipServer\MumbleServerBundle\Entity\Mumble'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dp_voipserver_mumbleserverbundle_mumble';
    }
}
