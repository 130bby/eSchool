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
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Main\UserBundle\Entity\ThemeUser as ThemeUser;
use Main\UserBundle\Entity\SavoirUser as SavoirUser;
use Main\UserBundle\Entity\EvaluationUser as EvaluationUser;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
		/** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

		$event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }


		$form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

			$currentRoute = $request->attributes->get('_route');
			if ($currentRoute == 'main_prof_register')
				$user->addRole('ROLE_PROF_TBC');
			elseif ($currentRoute == 'main_student_register')
			{
				$user->addRole('ROLE_ELEVE');
				$theme_id = $this->get('session')->get('theme_id');
				$savoir_user_data = $this->get('session')->get('savoir_user');
				$evaluation_user_data = $this->get('session')->get('evaluation_user');
				// si l'utilisateur a passé une épreuve avant de s'inscrire
				if (isset($theme_id))
				{
					$userManager->updateUser($user);
					$em = $this->getDoctrine()->getManager();
					$theme = $em->getRepository('MainThemeBundle:Theme')->find($theme_id);
					$theme_user = new ThemeUser();
					$theme_user->setUser($user);
					$theme_user->setTheme($theme);
					$theme_user->setDate(new \DateTime("now"));
					$em->persist($theme_user);
					
					//passage d'évaluation avant inscription
					if(isset($evaluation_user_data))
					{
						$userManager->updateUser($user);
						$em = $this->getDoctrine()->getManager();
						$evaluation = $em->getRepository('MainEvaluationBundle:Evaluation')->find($evaluation_user_data['evaluation_id']);
						
						$evaluation_user = new EvaluationUser();
						$evaluation_user->setUser($user);
						$evaluation_user->setEvaluation($evaluation);
						$evaluation_user->setScore($evaluation_user_data['score']);
						if ($evaluation_user_data['score'] > 70)
							$evaluation_user->setSuccess(1);
						else
							$evaluation_user->setSuccess(0);
						$evaluation_user->setTemps($evaluation_user_data['temps']);
						$evaluation_user->setDate(new \Datetime());
						$em->persist($evaluation_user);

						
						foreach($evaluation->getSavoirs() as $savoir_tba)
						{
							$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_tba);
							$savoir_user = new SavoirUser();
							$savoir_user->setUser($user);
							$savoir_user->setSavoir($savoir);
							$savoir_user->setScore($savoir->getScoreMini());
							$savoir_user->setSuccess($evaluation_user_data['success']);
							$savoir_user->setTemps($evaluation_user_data['temps']);
							$savoir_user->setDate(new \Datetime());
							$em->persist($savoir_user);
						}
						$em->flush();
					}

					//passage d'épreuve avant inscription
					if(isset($savoir_user_data))
					{
						$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_user_data['savoir_id']);
						$savoir_user = new SavoirUser();
						$savoir_user->setUser($user);
						$savoir_user->setSavoir($savoir);
						$savoir_user->setScore($savoir_user_data['score']);
						$savoir_user->setSuccess($savoir_user_data['success']);
						$savoir_user->setTemps($savoir_user_data['temps']);
						$savoir_user->setDate(new \Datetime());
						$em->persist($savoir_user);
						$em->flush();
					}
				}
			}
			$userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		return $this->redirect($this->generateUrl('main_user_profil'));
		/*
        return $this->render('FOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
        ));
		*/
    }
}
