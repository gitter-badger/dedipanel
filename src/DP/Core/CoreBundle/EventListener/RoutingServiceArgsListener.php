<?php

/**
 * This file is part of Dedipanel project
 *
 * (c) 2010-2014 Dedipanel <http://www.dedicated-panel.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DP\Core\CoreBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RoutingServiceArgsListener extends ContainerAware
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $params  = $request->attributes->get('_route_params');
        
        if (isset($params['_sylius']) && isset($params['_sylius']['criteria'])) {
            $params['_sylius']['criteria'] = $this->convertServiceArgs($params['_sylius']['criteria']);
            
            $request->attributes->set('_sylius', $params['_sylius']);
            $request->attributes->set('_route_params', $params);
        }
    }
    
    public function convertServiceArgs($criteria = array())
    {
        foreach ($criteria AS $key => $crit) {
            if (preg_match('#^\@(.+)\:(.+)$#', $crit, $matches)) {
                $service = $this->container->get($matches[1]);
                $value = $service->{$matches[2]}();
                
                if (is_array($value) && empty($value)) {
                    unset($criteria[$key]);
                }
                else {
                    $criteria[$key] = $value;
                }
            }
        }
        
        return $criteria;
    }
}
