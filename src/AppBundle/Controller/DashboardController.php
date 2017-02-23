<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Exception\LogicException;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @param Request       $request
     * @param UserInterface $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, UserInterface $customer)
    {
        return $this->render('dashboard/index.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/request-account-upgrade", name="request_account_upgrade")
     * @throws \LogicException
     */
    public function requestAccountUpgradeAction(UserInterface $customer)
    {
        try {

            $this->get('workflow.customer_signup')->apply($customer, 'request_account_upgrade');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);

        }

        return $this->redirectToRoute('dashboard');
    }
}
