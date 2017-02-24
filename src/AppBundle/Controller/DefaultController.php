<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Exception\LogicException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, UserInterface $user = null)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'customer' => $user
        ]);
    }


    /**
     * @Route("/request-transition", name="request_transition")
     * @throws \LogicException
     */
    public function requestTransitionAction()
    {
        try {

            $customer = $this->getDoctrine()->getRepository('AppBundle:Customer')->find(1);

            $this->get('workflow.customer_signup')->apply($customer, 'request_account_upgrade');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);

        }

        return $this->redirectToRoute('homepage');
    }
}
