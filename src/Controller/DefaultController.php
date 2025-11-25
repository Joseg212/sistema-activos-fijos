<?php

namespace App\Controller;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Intl\Languages;
use App\ClassPrivate\globalFunc;
use App\Entity\OpcionMenu;
use App\Entity\PermisoMenu;
use App\Entity\Usuario;
use Doctrine\Persistence\ManagerRegistry;



class DefaultController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        
        \Locale::setDefault('en');
    }

    /**
     * @Route("", name="app_default")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')){
            // get the login error if there is one
	    	$error = null;

	    	// last username entered by the user
	    	$lastUsername = "";

	        return $this->render('views/default/login.html.twig', [
	            'last_username' => $lastUsername,
	            'error'         => $error,
	        ]);

        } else {
            
            $objFunc = new globalFunc($doctrine);

            $idUsuario = $this->getUser()->getUserIdentifier();

            $arrMenu = $doctrine->getManager()->getRepository(PermisoMenu::class)->findByMenuOption($idUsuario);


	        return $this->render('views/default/dashboard.html.twig', [
	            'arrMenu' => $arrMenu,
	        ]);

        }
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
    	// get the login error if there is one
    	$error = $authenticationUtils->getLastAuthenticationError();

    	// last username entered by the user
    	$lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('views/default/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }


    /**
    * @Route("/logout", name="app_logout", methods={"GET"})
    */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
