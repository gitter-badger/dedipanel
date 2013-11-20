<?php

namespace DP\VoipServer\MumbleServerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DP\VoipServer\MumbleServerBundle\Entity\Mumble;
use DP\VoipServer\MumbleServerBundle\Form\Mumble\MumbleType;

/**
 * Machine controller.
 *
 */
class MachineController extends Controller
{
     /**
     * Lists all Mumble entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DPMumbleServerBundle:Mumble')->findAll();

        return $this->render('DPMumbleServerBundle:Machine:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Mumble entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Mumble();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('machine_mumble_show', array('id' => $entity->getId())));
        }

        return $this->render('DPMumbleServerBundle:Machine:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Mumble entity.
    *
    * @param Mumble $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Mumble $entity)
    {
        $form = $this->createForm(new MumbleType(), $entity, array(
            'action' => $this->generateUrl('machine_mumble_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Mumble entity.
     *
     */
    public function newAction()
    {
        $entity = new Mumble();
        $form   = $this->createCreateForm($entity);

        return $this->render('DPMumbleServerBundle:Machine:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Mumble entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DPMumbleServerBundle:Machine:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Mumble entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DPMumbleServerBundle:Machine:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Mumble entity.
    *
    * @param Mumble $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Mumble $entity)
    {
        $form = $this->createForm(new MumbleType(), $entity, array(
            'action' => $this->generateUrl('machine_mumble_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Mumble entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('machine_mumble_edit', array('id' => $id)));
        }

        return $this->render('DPMumbleServerBundle:Machine:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Mumble entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DPMumbleServerBundle:Mumble')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Mumble entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('machine'));
    }

    /**
     * Creates a form to delete a Mumble entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('machine_mumble_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
