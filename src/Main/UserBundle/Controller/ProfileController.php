<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Main\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		$em = $this->getDoctrine()->getManager();
		$themes = $em->getRepository('MainUserBundle:ThemeUser')->findByUser($user);
		$last_badge = $em->getRepository('MainUserBundle:BadgeUser')->findOneBy(array('user'=> $user),array('date' => 'DESC'));
		$last_epreuves = $em->getRepository('MainUserBundle:SavoirUser')->findBy(array('user'=> $user),array('date' => 'DESC'),4);
		$classes = array();
		$classes_user = $em->getRepository('MainUserBundle:ClasseUser')->findBy(array('user'=> $user),array('date' => 'DESC'));
		foreach ($classes_user as $classe_user)
		{
			$classes[] = $em->getRepository('MainClasseBundle:Classe')->find($classe_user->getClasse());
		}
		$classe_ownership = $em->getRepository('MainClasseBundle:Classe')->findBy(array('owner'=> $user));

        return $this->render('MainUserBundle:Profile:show.html.twig', array(
            'user' => $user,'themes' => $themes,'last_badge' => $last_badge,'last_epreuves' => $last_epreuves,'classes' => $classes,'classe_ownership' => $classe_ownership
        ));
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('MainUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
