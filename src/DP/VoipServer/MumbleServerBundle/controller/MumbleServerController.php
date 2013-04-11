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
        
		$entity = $entities[0];
		$query = TaClass($entity);
		var_dump($query->getUsers());
		// Ca fonctionne aussi bien ^^
		// fais le service après
		// et le listener en dernier
		// (quand t'arrivers à récupérer une instance via le service dans le controller en gros)
		//var_dump($entity->getQuery());
		
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
                $status = $entity->installServer($twig);
            }
        
            $entity->setInstallationStatus($status);

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
        $editForm = $this->createForm(new BaseMumbleServerType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            
            $twig = $this->get('twig');
            
            $em->persist($entity);
            $em->flush();

            $entity->uploadIniScript($twig); 
            if($entity->getStateStatus() != 0){
                $entity->changeStateServer('restart');
            }   
            
            return $this->redirect($this->generateUrl('mumble_edit', array('id' => $id)));
        }

        return $this->render('DPMumbleServerBundle:MumbleServer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Verification if mumble is installed
     *
     */
    public function installAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);
        $twig = $this->get('twig');
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SteamServer entity.');
        }
        
        // on verifie avant toute chose que tout soit à jour avec le serveur
        // et on enregistre tout dans la BDD
        $status = $entity->verificationServer();
        
        // Si tout est bon c'est ok, sinon on upload le shell ou alors on refait l'install
        if($status != 2) {
            if($status != 1){   
                $delete = $entity->removeServer();
                if($delete == 0) {
                   $entity->installServer($this->get('twig'));
                   $status = $entity->verificationServer(); 
                }
            }
            else {
                $entity->uploadShellScripts($twig);
                $status = $entity->verificationServer();
            }
        }
        
        $entity->setInstallationStatus($status);
        
        $em->persist($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('mumble'));
    }
    /*
     * Change State of server
     */
    public function changeStateAction($id, $state)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MumbleServer entity.');
        }
        
        $status = $entity->changeStateServer($state);
        $entity->setStateStatus($status);
        
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
            
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DPVoipServerBundle:VoipServer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MumbleServer entity.');
            }

            $delete = $entity->removeServer();
            
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
