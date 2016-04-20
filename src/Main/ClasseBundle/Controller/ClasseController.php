<?php

namespace Main\ClasseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Main\ClasseBundle\Entity\Classe;
use Main\ClasseBundle\Form\ClasseType;
use Main\UserBundle\Entity\ClasseUser;

/**
 * Classe controller.
 *
 * @Route("/classe")
 */
class ClasseController extends Controller
{

    /**
     * Lists all Classe entities.
     *
     * @Route("/", name="classe")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$user = $this->container->get('security.context')->getToken()->getUser();

        // $entities = $em->getRepository('MainClasseBundle:Classe')->findAll();
        $entities = $em->getRepository('MainClasseBundle:Classe')->findBy(array('owner' => $user->getId()));
		$nb_eleves = array();
		foreach ($entities as $entity)
		{
			$nb_eleves[] = count($eleves = $em->getRepository('MainUserBundle:ClasseUser')->findBy(array('classe' => $entity->getId())));
		}

        return array(
            'entities' => $entities,
            'nb_eleves' => $nb_eleves,
        );
    }
	
    /**
     * Apply to class.
     *
     * @Route("/apply", name="classe_apply")
     * @Method("GET")
     * @Template()
     */
    public function applyAction()
    {
        $em = $this->getDoctrine()->getManager();
		$request = $this->container->get('request');
		$code = $request->query->get('code');
		$user = $this->container->get('security.context')->getToken()->getUser();

        $classe = $em->getRepository('MainClasseBundle:Classe')->findOneBy(array('code' => $code));
		if ($classe != null)
		{
			$classe_user = new ClasseUser();
			$classe_user->setUser($user);
			$classe_user->setClasse($classe);
			$classe_user->setDate(new \Datetime());
			$em->persist($classe_user);
            $em->flush();
			$this->addFlash('success', 'Félicitations ! vous participez désormais à la classe : '.$classe->getName());
		}
		else
			$this->addFlash('error', 'Oups ! le code que tu as rentré est erroné !');
		
		
		return $this->redirect($this->generateUrl('main_user_profil'));

    }	
	
    /**
     * Remove from class.
     *
     * @Route("/remove_user", name="classe_remove_user")
     * @Method("GET")
     * @Template()
     */
    public function RemoveUserAction()
    {
        $em = $this->getDoctrine()->getManager();
		$request = $this->container->get('request');
		$user_tbr_p = $request->query->get('user');
		$classe_p = $request->query->get('classe');
		$user = $this->container->get('security.context')->getToken()->getUser();

        $classe = $em->getRepository('MainClasseBundle:Classe')->find($classe_p);
        $user_tbr = $em->getRepository('MainUserBundle:User')->find($user_tbr_p);
        $au_revoir = $em->getRepository('MainUserBundle:ClasseUser')->findOneBy(array('user' => $user_tbr, 'classe' => $classe));
		if ($au_revoir != null)
		{
			$em->remove($au_revoir);
            $em->flush();
			$this->addFlash('success', "Vous avez supprimé l'élève : ".$user_tbr->getUsername()." avec succès.");
		}
		
		
        return $this->redirect($this->generateUrl('classe_show', array('id' => $classe->getId())));
    }	
	
	
    /**
     * Creates a new Classe entity.
     *
     * @Route("/", name="classe_create")
     * @Method("POST")
     * @Template("MainClasseBundle:Classe:new.html.twig")
     */
    public function createAction(Request $request)
    {
		$user= $this->get('security.context')->getToken()->getUser();
		$this->denyAccessUnlessGranted('ROLE_PROF', $user, 'Attention à Seth, il va encore se mettre en colère !');
        $entity = new Classe();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
			$entity->setOwner($user);
			$em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('classe_show', array('id' => $entity->getId())));
        }

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
    private function createCreateForm(Classe $entity)
    {
        $form = $this->createForm(new ClasseType(), $entity, array(
            'action' => $this->generateUrl('classe_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Displays a form to create a new Classe entity.
     *
     * @Route("/new", name="classe_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Classe();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Classe entity.
     *
     * @Route("/{id}", name="classe_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($id);
		$eleves = null;
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
        }
		else
			$eleves = $em->getRepository('MainUserBundle:ClasseUser')->findBy(array('classe' => $entity->getId()));

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'eleves'      => $eleves,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Classe entity.
     *
     * @Route("/{id}/edit", name="classe_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
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
    * Creates a form to edit a Classe entity.
    *
    * @param Classe $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Classe $entity)
    {
        $form = $this->createForm(new ClasseType(), $entity, array(
            'action' => $this->generateUrl('classe_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

        return $form;
    }
    /**
     * Edits an existing Classe entity.
     *
     * @Route("/{id}", name="classe_update")
     * @Method("PUT")
     * @Template("MainClasseBundle:Classe:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MainClasseBundle:Classe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classe entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('classe_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Classe entity.
     *
     * @Route("/{id}", name="classe_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MainClasseBundle:Classe')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Classe entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('classe'));
    }

    /**
     * Creates a form to delete a Classe entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('classe_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer'))
            ->getForm()
        ;
    }
	
}
