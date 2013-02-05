<?php

namespace DP\VoipServer\MumbleServerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DP\VoipServer\MumbleServerBundle\Entity\MumbleServer;
use DP\VoipServer\MumbleServerBundle\Form\BaseMumbleServerType;
use DP\VoipServer\MumbleServerBundle\Form\InstallMumbleServerType;

/**
 * MumbleServer controller.
 *
 */
class MumbleServerController extends Controller
{
    /**
     * Lists all MumbleServer entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DPVoipServerBundle:VoipServer')->findAll();

        return $this->render('DPMumbleServerBundle:MumbleServer:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a MumbleServer entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VoipServer entity.');
        }
    
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DPMumbleServerBundle:MumbleServer:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),       
        ));
    }

    /**
     * Displays a form to create a new MumbleServer entity.
     *
     */
    public function newAction()
    {
        $entity = new MumbleServer();
        $form   = $this->createForm(new InstallMumbleServerType(), $entity);

        return $this->render('DPMumbleServerBundle:MumbleServer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new MumbleServer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new MumbleServer();
        $form = $this->createForm(new InstallMumbleServerType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            
            $install = $form->get('install')->getData();
            $twig = $this->get('twig');
            
            // On lance l'installation si le serveur n'est pas déjà sur la machine
            if (!$install) {
                $entity->installServer($twig);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mumble_show', array('id' => $entity->getId())));
        }
        
        return $this->render('DPMumbleServerBundle:MumbleServer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MumbleServer entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MumbleServer entity.');
        }

        $editForm = $this->createForm(new BaseMumbleServerType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DPMumbleServerBundle:MumbleServer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing MumbleServer entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MumbleServer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MumbleServerType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mumble_edit', array('id' => $id)));
        }

        return $this->render('DPMumbleServerBundle:MumbleServer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Verification if mumble was installed
     *
     */
    public function installAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SteamServer entity.');
        }
        
        $status = $entity->getInstallationStatus();
        
        // On upload le scipt pour lancer,stopper la machine
        if ($status == 1) {
            $entity->uploadShellScripts($this->get('twig'));
        }  
        // On vérifie que l'installation n'est pas bloqué (si c'est le cas on la relance)
        // L'install est extremement rapide, donc si elle n'est pas fini, c'est qu'il y a un problème
        elseif ($status == 0) {
            
            $delete = $entity->removeServer($this->get('twig'));
            
            if($delete == 0)
                $entity->installServer($this->get('twig'));
                $this->setInstallationStatus (1);
        }

        $em->persist($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('mumble'));
    }
    /**
     * Deletes a MumbleServer entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            
            $twig = $this->get('twig');
            
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MumbleServer entity.');
            }

            $delete = $entity->removeServer($twig);
            
            if($delete === 0){
                $em->remove($entity);
                $em->flush();
            }
            else{
                return $this->redirect($this->generateUrl('mumble'));
            }
            
        }

        return $this->redirect($this->generateUrl('mumble'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
