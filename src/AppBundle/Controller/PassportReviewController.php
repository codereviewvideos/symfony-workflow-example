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
 * @Route("passport-review")
 */
class PassportReviewController extends Controller
{
    /**
     * @Route("/", name="passport_review")
     */
    public function listPassportsForReviewAction(Request $request)
    {
        $passports = $this->getDoctrine()->getRepository('AppBundle:Customer')
            ->findAllAwaitingManualPassportReview()
            ->getResult()
        ;

        return $this->render('passport/passport-review.html.twig', [
            'passports_awaiting_review' => $passports
        ]);
    }

    /**
     * @Route("/approve/{id}", name="manual_passport_approve")
     * @throws \LogicException
     */
    public function manuallyApprovePassportAction(Request $request, $id)
    {
        $passport = $this->getDoctrine()->getRepository('AppBundle:Customer')
            ->find($id);

        try {

            $this->get('workflow.customer_signup')->apply($passport, 'manual_passport_approval');

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Approved');

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

        }

        return $this->redirectToRoute('passport_review');
    }

    /**
     * @Route("/decline/{id}", name="manual_passport_decline")
     * @throws \LogicException
     */
    public function manuallyDeclinePassportAction(Request $request, $id)
    {
        $passport = $this->getDoctrine()->getRepository('AppBundle:Customer')
            ->find($id);

        try {

            $this->get('workflow.customer_signup')->apply($passport, 'decline_passport');

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('warning', 'Declined');

        } catch (LogicException $e) {

            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));

        }

        return $this->redirectToRoute('passport_review');
    }
}
