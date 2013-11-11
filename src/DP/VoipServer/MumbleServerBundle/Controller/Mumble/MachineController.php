<?php

namespace DP\VoipServer\MumbleServerBundle\Controller\Mumble;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DP\VoipServer\MumbleServerBundle\Entity\Mumble\Machine;
use DP\VoipServer\MumbleServerBundle\Form\Mumble\MachineType;

/**
 * Mumble\Machine controller.
 *
 * @Route("/mumble_machine")
 */
class MachineController extends Controller
{

    /**
     * Lists all Mumble\Machine entities.
     *
     * @Route("/", name="mumble_machine")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DPMumbleServerBundle:Mumble\Machine')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Mumble\Machine entity.
     *
     * @Route("/", name="mumble_machine_create")
     * @Method("POST")
     * @Template("DPMumbleServerBundle:Mumble\Machine:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Machine();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mumble_machine_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Mumble\Machine entity.
    *
    * @param Machine $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Machine $entity)
    {
        $form = $this->createForm(new MachineType(), $entity, array(
            'action' => $this->generateUrl('mumble_machine_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Mumble\Machine entity.
     *
     * @Route("/new", name="mumble_machine_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Machine();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Mumble\Machine entity.
     *
     * @Route("/{id}", name="mumble_machine_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble\Machine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble\Machine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Mumble\Machine entity.
     *
     * @Route("/{id}/edit", name="mumble_machine_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble\Machine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble\Machine entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Mumble\Machine entity.
    *
    * @param Machine $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Machine $entity)
    {
        $form = $this->createForm(new MachineType(), $entity, array(
            'action' => $this->generateUrl('mumble_machine_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Mumble\Machine entity.
     *
     * @Route("/{id}", name="mumble_machine_update")
     * @Method("PUT")
     * @Template("DPMumbleServerBundle:Mumble\Machine:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DPMumbleServerBundle:Mumble\Machine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mumble\Machine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mumble_machine_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Mumble\Machine entity.
     *
     * @Route("/{id}", name="mumble_machine_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DPMumbleServerBundle:Mumble\Machine')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Mumble\Machine entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mumble_machine'));
    }

    /**
     * Creates a form to delete a Mumble\Machine entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mumble_machine_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
