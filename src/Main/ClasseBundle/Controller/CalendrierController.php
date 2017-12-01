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

        $form->add('submit', 'submit', array('label' => 'Créer le calendrier','attr' => array('class' => 'submit_classe', 'style' => 'margin-bottom:40px;margin-left:12px;width:35%')));

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
		if (!$this->isGranted('ROLE_PROF'))
			return $this->redirectToRoute('main_home_homepage');
		$form   = $this->createCreateForm($classe_id);
		$em = $this->getDoctrine()->getManager();
		$classe = $em->getRepository('MainClasseBundle:Classe')->find($classe_id);

        return array(
            'form'   => $form->createView(),
            'classe_id'   => $classe_id,
            'classe_name'   => $classe->getName(),
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
		$user = $this->container->get('security.context')->getToken()->getUser();
		foreach ($entity->getCalendrier() as $cal_element)
		{
			$scores = array();
			$savoirs_passed = $em->getRepository('MainUserBundle:SavoirUser')->findby(array("user" => $user,"savoir" => $cal_element->getSavoir()));
			foreach ($savoirs_passed as $savoir_passed)
				$scores[] = $savoir_passed->getScore();
			if (!empty($scores) && max($scores) > 80)			
				$passed[$i] = 1;
			else
				$passed[$i] = 0;
			$calendar_elements[$i]['id'] = $cal_element->getSavoir()->getId();
			$calendar_elements[$i]['title'] = $cal_element->getSavoir()->getName();
			$calendar_elements[$i]['start'] = $cal_element->getStart()->format('Y-m-d');
			$calendar_elements[$i]['end'] = $cal_element->getEnd()->format('Y-m-d');
			if (!empty($scores))
				$calendar_elements[$i]['strength'] = round(max($scores)/20);
			else
				$calendar_elements[$i]['strength'] = 0;
			if ($passed[$i] == 1)
				$calendar_elements[$i]['backgroundColor'] = '#5cb85c';
			elseif (!empty($scores) && max($scores) > 60)			
				$calendar_elements[$i]['backgroundColor'] = '#fdd835';
			elseif ((empty($scores) || max($scores) < 60) && ($cal_element->getEnd() < new \DateTime("now")))			
				$calendar_elements[$i]['backgroundColor'] = '#d9534f';
			elseif($cal_element->getStart() > new \DateTime("now"))
				$calendar_elements[$i]['backgroundColor'] = '#9e9e9e';
			else
				$calendar_elements[$i]['backgroundColor'] = '#f39f03';
			$i++;
		}

        return array(
            'entity'      => $entity,
            'passed'=> $passed,
            'calendar_elements'=> $calendar_elements,
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
		if (!$this->isGranted('ROLE_PROF'))
			return $this->redirectToRoute('main_home_homepage');
		
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

        $form->add('submit', 'submit', array('label' => 'Mettre à jour','attr' => array('class' => 'submit_classe', 'style' => 'margin-top:40px;margin-left:12px;width:35%')));

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
            ->add('submit', 'submit', array('label' => 'Supprimer le calendrier','attr' => array('class' => 'submit_classe', 'style' => 'margin-left:12px;width:35%;background-color:#B22222;')))
            ->getForm()
        ;
    }	
}
