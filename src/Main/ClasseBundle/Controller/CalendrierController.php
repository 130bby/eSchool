<?php

namespace Main\ClasseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Main\ClasseBundle\Entity\Calendrier;
use Main\ClasseBundle\Entity\Classe;
use Main\ClasseBundle\Form\CalendrierType;

/**
 * Classe controller.
 *
 * @Route("/calendrier")
 */
class CalendrierController extends Controller
{

    /**
     * Creates a new Classe entity.
     *
     * @Route("/", name="calendrier_create")
     * @Method("POST")
     * @Template("MainClasseBundle:Classe:new.html.twig")
     */
    public function createAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$user= $this->get('security.context')->getToken()->getUser();
		$this->denyAccessUnlessGranted('ROLE_PROF', $user, 'Attention à Seth, il va encore se mettre en colère !');
		
		$elements = $request->request->get('main_classebundle_calendrier');
		
		$entity = $em->getRepository('MainClasseBundle:Classe')->find($request->request->get('classe'));
        
		$form = $this->createCreateForm($entity);
		foreach ($elements["elements"] as $element)
		{
			$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($element["savoir"]);
			$calendarElem = new Calendrier();
			$calendarElem->setClasse($entity);
			$calendarElem->setSavoir($savoir);
			$calendarElem->setStart(new \Datetime($element["start"]["year"].'-'.$element["start"]["month"].'-'.$element["start"]["day"]));
			$calendarElem->setEnd(new \Datetime($element["end"]["year"].'-'.$element["end"]["month"].'-'.$element["end"]["day"]));
            $em->persist($calendarElem);
		}
        $em->flush();
        return $this->redirect($this->generateUrl('calendrier_show', array('classe_id' => $entity->getId())));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Classe entity.
     *
     * @param Classe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($classe_id)
    {
		$em = $this->getDoctrine()->getManager();
		$classe = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);
        $form = $this->createForm(new CalendrierType($classe,$this->container), null, array(
            'action' => $this->generateUrl('calendrier_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Displays a form to create a new Classe entity.
     *
     * @Route("/{classe_id}/new", name="calendrier_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($classe_id)
    {
		$form   = $this->createCreateForm($classe_id);

        return array(
            'form'   => $form->createView(),
            'classe_id'   => $classe_id,
        );
    }
	
    /**
     * Finds and displays a Examen entity.
     *
     * @Route("/{classe_id}", name="calendrier_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($classe_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
        }
			
        $deleteForm = $this->createDeleteForm($classe_id);
		$i = 0;
		foreach ($entity->getCalendrier() as $cal_element)
		{
			$gantt_values[$i]['from'] = "/Date(".$cal_element->getStart()->getTimestamp()."000)/";
			$gantt_values[$i]['to'] = "/Date(".$cal_element->getEnd()->getTimestamp()."000)/";
			$gantt_values[$i]['desc'] = $cal_element->getSavoir()->getName();
			$gantt_values[$i]['label'] = $cal_element->getSavoir()->getName();
			$gantt_data[$i]["values"][0] = $gantt_values[$i];
			$i++;
		}

        return array(
            'entity'      => $entity,
            'gantt_data'=> json_encode($gantt_data),
            'delete_form' => $deleteForm->createView(),
        );
    }	
	
    /**
     * Displays a form to edit an existing Examen entity.
     *
     * @Route("/{classe_id}/edit", name="calendrier_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($classe_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($classe_id);

        return array(
            'classe_id'   => $classe_id,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Calendrier entity.
    *
    * @param Classe $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Classe $entity)
    {
        $form = $this->createForm(new CalendrierType($entity,$this->container), $entity, array(
            'action' => $this->generateUrl('calendrier_update', array('classe_id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Calendrier entity.
     *
     * @Route("/{classe_id}", name="calendrier_update")
     * @Method("PUT")
     * @Template("MainClasseBundle:Examen:edit.html.twig")
     */
    public function updateAction(Request $request, $classe_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
        }
		
		$elements = $request->request->get('main_classebundle_calendrier');
		foreach ($entity->getCalendrier() as $cal_elem)
			$em->remove($cal_elem);
		$em->flush();
		foreach ($elements["elements"] as $element)
		{
			$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($element["savoir"]);
			$calendarElem = new Calendrier();
			$calendarElem->setClasse($entity);
			$calendarElem->setSavoir($savoir);
			$calendarElem->setStart(new \Datetime($element["start"]["year"].'-'.$element["start"]["month"].'-'.$element["start"]["day"]));
			$calendarElem->setEnd(new \Datetime($element["end"]["year"].'-'.$element["end"]["month"].'-'.$element["end"]["day"]));
            $em->persist($calendarElem);
		}
		
		$em->flush();
		return $this->redirect($this->generateUrl('calendrier_edit', array('classe_id' => $classe_id)));
    }
    /**
     * Deletes a Examen entity.
     *
     * @Route("/{classe_id}", name="calendrier_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $classe_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);
		foreach ($entity->getCalendrier() as $cal_elem)
			$em->remove($cal_elem);
		$em->flush();
        return $this->redirect($this->generateUrl('classe'));
    }

    /**
     * Creates a form to delete a Calendrier entity by id.
     *
     * @param mixed $classe_id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($classe_id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('calendrier_delete', array('classe_id' => $classe_id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }	
}
