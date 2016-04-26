<?php

namespace Main\ClasseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Main\ClasseBundle\Entity\Examen;
use Main\ClasseBundle\Form\ExamenType;
use Main\UserBundle\Entity\ExamenUser;
use Main\UserBundle\Entity\SavoirUser as SavoirUser;
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
     * Lists all Examen entities for a user.
     *
     * @Route("/suivi", name="suivi_examen")
     * @Method("GET")
     * @Template("MainClasseBundle:Examen:index.html.twig")
     */
    public function suiviAction()
    {
        $em = $this->getDoctrine()->getManager();
        $examens = $em->getRepository('MainClasseBundle:Examen')->getExamens($this->container->get('security.context')->getToken()->getUser(),$em);
        return array(
            'entities' => $examens,
        );
    }
	
    /**
     * Pass examen for an eleve.
     *
     * @Route("/suivi/pass/{id}", name="examen_pass")
     * @Method("GET")
     * @Template()
     */
    public function passAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $examen = $em->getRepository('MainClasseBundle:Examen')->find($id);
		$exercices = $em->createQuery('SELECT e FROM MainExerciceBundle:Exercice e WHERE e.id IN ('.implode(',',$examen->getExercices()).')')->getArrayResult();

        return array(
            'examen' => $examen,
            'exercices' => $exercices,
        );
    }
	
    /**
     * Passed examen for an eleve.
     *
     * @Route("/suivi/passed/{examen_id}", name="examen_passed")
     * @Method("POST")
     * @Template()
     */
    public function passedAction($examen_id)
    {
		$request = $this->getRequest()->request;
        $em = $this->getDoctrine()->getManager();
        $examen = $em->getRepository('MainClasseBundle:Examen')->find($examen_id);
		
		//gestion du temps
		$temps_limite = new \DateTime('00:15:00');
		$temps_intervalle = $temps_limite->diff(new \Datetime('00:'.$request->get('temps')));
		$temps = new \DateTime('00:00:00');
		$temps->sub($temps_intervalle);
		
		$score = (int)$request->get('score')*100/(count($examen->getSavoirs())*6);
		$savoirs = array();

		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
		{
			$examen_user = new ExamenUser();
			$examen_user->setUser($this->container->get('security.context')->getToken()->getUser());
			$examen_user->setExamen($examen);
			$examen_user->setScore($score);
			if ($score > 70)
				$examen_user->setSuccess(1);
			else
				$examen_user->setSuccess(0);
			$examen_user->setTemps($temps);
			$examen_user->setDate(new \Datetime());
			$em->persist($examen_user);
		}
		if ($score > 70)
		{
			foreach ($examen->getSavoirs() as $savoir_id)
			{
				$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
				$savoirs[] = $savoir;

				if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
				{
					$savoir_user = new SavoirUser();
					$savoir_user->setUser($this->container->get('security.context')->getToken()->getUser());
					$savoir_user->setSavoir($savoir);
					$savoir_user->setScore($savoir->getScoreMini());
					$savoir_user->setTemps($temps);
					$savoir_user->setSuccess(true);
					$savoir_user->setDate(new \Datetime());
					$em->persist($savoir_user);
				}
			}
		}

		$em->flush();
		if ($score > 70)
			$success = true;
		else
			$success = false;
		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
			$badges = array();
        
		return $this->render('MainClasseBundle:Examen:passed.html.twig', array('success' => $success, 'examen' => $examen,'savoirs' => $savoirs, 'badges' => $badges));
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
        $em = $this->getDoctrine()->getManager();
        $entity = new Examen();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
		$request_form = $request->request->get('main_classebundle_examen');
		$savoirs = array();
		foreach ($request_form['savoirs'] as $savoir)
			$savoirs[] = $savoir['savoir'];
        $entity->setSavoirs($savoirs);
		$exercices = array();
		foreach ($savoirs as $savoir_id)
		{
			$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
			$exercices = array_merge($exercices, $em->getRepository('MainExerciceBundle:Exercice')->getExercicesEpreuve($savoir,false));
		}
		$exercices_ids = array();
		foreach ($exercices as $exercice)
			$exercices_ids[] = $exercice['id'];
        $entity->setExercices($exercices_ids);
		// if ($form->isValid()) {
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
