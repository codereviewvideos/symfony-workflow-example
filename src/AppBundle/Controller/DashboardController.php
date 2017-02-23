<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        if ($this->get('workflow.customer_signup')->getMarking($customer)->has('declined')) {
            return $this->redirectToRoute('denied');
        }

        if ($this->get('workflow.customer_signup')->getMarking($customer)->has('paying_customer')) {
            return $this->redirectToRoute('success');
        }

        return $this->render('dashboard/index.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/denied", name="denied")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deniedAction()
    {
        return $this->render('dashboard/denied.html.twig');
    }

    /**
     * @Route("/success", name="success")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction()
    {
        return $this->render('dashboard/success.html.twig');
    }

    /**
     * @Route("/request-account-upgrade", name="request_account_upgrade")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function requestAccountUpgradeAction(Request $request, UserInterface $customer)
    {
        try {
            $this->get('workflow.customer_signup')->apply($customer, 'request_account_upgrade');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/add-passport", name="add_passport")
     */
    public function addPassportAction(Request $request)
    {
        return $this->render('dashboard/add-passport.html.twig');
    }

    /**
     * @Route("/submit-passport", name="submit_passport")
     * @Method("POST")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function submitPassportAction(Request $request, UserInterface $customer)
    {
        try {
            $this->get('workflow.customer_signup')->apply($customer, 'add_passport');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);
        }

        return $this->redirectToRoute('dashboard');
    }



    /**
     * @Route("/add-card-details", name="add_card_details")
     */
    public function addCardDetailsAction(Request $request)
    {
        return $this->render('dashboard/add-card-details.html.twig');
    }

    /**
     * @Route("/submit-card-details", name="submit_card_details")
     * @Method("POST")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function submitCardDetailsAction(Request $request, UserInterface $customer)
    {
        try {
            $this->get('workflow.customer_signup')->apply($customer, 'add_card_details');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);
        }

        return $this->redirectToRoute('dashboard');
    }




    /**
     * @Route("/upgrade-account", name="upgrade_account")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function upgradeAccountAction(Request $request, UserInterface $customer)
    {
        try {
            $this->get('workflow.customer_signup')->apply($customer, 'upgrade_customer');

            $this->getDoctrine()->getManager()->flush();

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

            $this->get('logger')->error('Yikes!', ['error' => $e->getMessage()]);
        }

        return $this->redirectToRoute('dashboard');
    }
}
