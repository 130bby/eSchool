<?php

namespace Main\UserBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends Controller
{
    public function confirmAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $user = $this->admin->getObject($id);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('unable to find the user with id : %s', $id));
        }

		$user->addRole('ROLE_PROF');
		$user->removeRole('ROLE_PROF_TBC');
        $userManager = $this->get('fos_user.user_manager');
		$userManager->updateUser($user);

        $this->addFlash('sonata_flash_success', 'prof confirmé !');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}