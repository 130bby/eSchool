<?php

namespace Main\ClasseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Main\ClasseBundle\Entity\Examen;
use Main\ClasseBundle\Form\ExamenType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Examen controller.
 *
 * @Route("/examen")
 */
class ExamenController extends Controller
{

    /**
     * Lists all Examen entities.
     *
     * @Route("/classe/{id}", name="examen")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MainClasseBundle:Examen')->findBy(array('classe' => $id));

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Examen entity.
     *
     * @Route("/", name="examen_create")
     * @Method("POST")
     * @Template("MainClasseBundle:Examen:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Examen();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
		$request_form = $request->request->get('main_classebundle_examen');
		$savoirs = array();
		foreach ($request_form['savoirs'] as $savoir)
			$savoirs[] = $savoir['savoir'];
        $entity->setSavoirs($savoirs);
		// if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('examen_show', array('id' => $entity->getId())));
        // }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Examen entity.
     *
     * @param Examen $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Examen $entity)
    {
        $form = $this->createForm(new ExamenType($this->container), $entity, array(
            'action' => $this->generateUrl('examen_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Examen entity.
     *
     * @Route("/new", name="examen_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Examen();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Examen entity.
     *
     * @Route("/{id}", name="examen_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Examen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Examen entity.');
        }
		$savoirs = array();
		foreach ($entity->getSavoirs() as $savoir)
			$savoirs[] = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir);
			
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'savoirs'      => $savoirs,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Examen entity.
     *
     * @Route("/{id}/edit", name="examen_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Examen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Examen entity.');
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
    * Creates a form to edit a Examen entity.
    *
    * @param Examen $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Examen $entity)
    {
        $form = $this->createForm(new ExamenType(), $entity, array(
            'action' => $this->generateUrl('examen_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Examen entity.
     *
     * @Route("/{id}", name="examen_update")
     * @Method("PUT")
     * @Template("MainClasseBundle:Examen:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Examen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Examen entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('examen_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Examen entity.
     *
     * @Route("/{id}", name="examen_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MainClasseBundle:Examen')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Examen entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('examen'));
    }

    /**
     * Creates a form to delete a Examen entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('examen_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
	
    /**
     * Refresh Calendar.
     *
     * @Route("/ajax/getSavoirsFromTheme", name="get_savoirs_from_theme")
     * @Method("POST")
     */
    public function refreshCalendar(Request $request)
    {
		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$savoirs_array = array();
			$savoirs = $em->getRepository('MainSavoirBundle:Savoir')->findBy(array("theme" => $this->container->get('request')->request->get('theme')));
			foreach ($savoirs as $savoir)
				$savoirs_array[$savoir->getId()] = $savoir->getName();
			$json = json_encode($savoirs_array);
			$response = new Response($json, 200);
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
    }
	
}
