<?php

namespace Main\UserBundle\Redirection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;
    private $em;
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router,$em)
    {
        $this->router = $router;
        $this->em = $em;
    }
    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // Get list of roles for current user
        $roles = $token->getRoles();
        // Tranform this list in array
        $rolesTab = array_map(function($role){ 
          return $role->getRole(); 
        }, $roles);
        // If is a admin or super admin we redirect to the backoffice area
        if (in_array('ROLE_ADMIN', $rolesTab, true) || in_array('ROLE_SUPER_ADMIN', $rolesTab, true))
            $redirection = new RedirectResponse($this->router->generate('sonata_admin_redirect'));
        // otherwise, if is a commercial user we redirect to the crm area
        elseif (in_array('ROLE_USER', $rolesTab, true))
		{
			$last_epreuve = $this->em->getRepository('MainUserBundle:SavoirUser')->findOneBy(array('user' => $token->getUser()->getId()),array('date' => 'DESC'));
			if ($last_epreuve)
			{
				$savoir = $this->em->getRepository('MainSavoirBundle:Savoir')->find($last_epreuve->getSavoir()->getId());
				$redirection = new RedirectResponse($this->router->generate('main_savoir_arbre_theme', array('theme_id' => $savoir->getTheme()->getId())));
			}
			else
				$redirection = new RedirectResponse($this->router->generate('fos_user_profile_show'));
		}
        // otherwise we redirect user to the member area
        else
            $redirection = new RedirectResponse($this->router->generate('fos_user_profile_show'));
        
        return $redirection;
    }
} 