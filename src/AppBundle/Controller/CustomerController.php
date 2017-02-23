<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CustomerType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Customer controller.
 *
 * @Route("customer")
 */
class CustomerController extends Controller
{
    /**
     * Lists all customer entities.
     *
     * @Route("/", name="customer_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('AppBundle:Customer')->findAll();

        return $this->render('customer/index.html.twig', array(
            'customers' => $customers,
        ));
    }

    /**
     * Creates a new customer entity.
     *
     * @Route("/new", name="customer_new")
     * @Method({"GET", "POST"})
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Workflow\Exception\LogicException
     */
    public function newAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->get('security.password_encoder')->encodePassword(
                $customer,
                $customer->getPlainPassword()
            );

            $customer->setPassword($password);

            $this->get('workflow.customer_signup')->apply(
                $customer,
                'sign_up'
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush($customer);

            $token = new UsernamePasswordToken(
                $customer,
                null,
                'main',
                $customer->getRoles()
            );
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('customer_show', array('id' => $customer->getId()));
        }

        return $this->render('customer/new.html.twig', array(
            'customer' => $customer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a customer entity.
     *
     * @Route("/{id}", name="customer_show")
     * @Method("GET")
     */
    public function showAction(Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);

        return $this->render('customer/show.html.twig', array(
            'customer' => $customer,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing customer entity.
     *
     * @Route("/{id}/edit", name="customer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);
        $editForm = $this->createForm('AppBundle\Form\CustomerType', $customer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $password = $this->get('security.password_encoder')->encodePassword(
                $customer,
                $customer->getPlainPassword()
            );

            $customer->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_edit', array('id' => $customer->getId()));
        }

        return $this->render('customer/edit.html.twig', array(
            'customer' => $customer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a customer entity.
     *
     * @Route("/{id}", name="customer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Customer $customer)
    {
        $form = $this->createDeleteForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush($customer);
        }

        return $this->redirectToRoute('customer_index');
    }

    /**
     * Creates a form to delete a customer entity.
     *
     * @param Customer $customer The customer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Customer $customer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customer_delete', array('id' => $customer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
