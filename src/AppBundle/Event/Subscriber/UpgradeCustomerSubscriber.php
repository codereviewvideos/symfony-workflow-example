<?php

namespace AppBundle\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Workflow;

class UpgradeCustomerSubscriber implements EventSubscriberInterface
{
    private $workflow;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Workflow $workflow, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->workflow = $workflow;
        $this->logger = $logger;
        $this->em = $em;
    }

    public function onLeave(Event $event)
    {
        $this->logger->debug('on leave', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public function onTransition(Event $event)
    {
        $this->logger->debug('on transition', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);

    }

    public function onEnter(Event $event)
    {
        $this->logger->debug('on enter', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public function onEntered(Event $event)
    {
        $this->logger->debug('on enterED', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public function onFreeCustomer(Event $event)
    {
        $this->logger->debug('on enterED Free Customer', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public function onEnterDeclinedPassport(Event $event)
    {
        $this->logger->debug('on enter DECLINED PASSPORT called?');

        if ($event->getTransition()->getName() !== 'decline_passport') {
            return false;
        }

        $this->logger->debug('on enter DECLINED PASSPORT', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);


        if ($this->workflow->can($event->getSubject(), 'declined_card_details')) {
            $this->workflow->apply($event->getSubject(), 'declined_card_details');
        }

        $this->em->flush();
    }

    public function onAnnounceDeclined(Event $event)
    {
        $this->logger->debug('on announce DECLINED', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);

//        $this->workflow->
    }

    public function onAnnounce(Event $event)
    {
        $this->logger->debug('on announce', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);

//        $this->workflow->apply($event->getSubject(), 'converted_from_thank_you_email');
    }

    public function onAnnounceRAU(Event $event)
    {
        $this->logger->debug('on announce RAU', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);

//        $this->workflow->apply($event->getSubject(), 'converted_from_thank_you_email');
    }

    public function onAnnounceAV(Event $event)
    {
        $this->logger->debug('on announce AV', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);

//        $this->workflow->apply($event->getSubject(), 'converted_from_thank_you_email');
    }

    public function onAwaitingPassport(Event $event)
    {
        $this->logger->debug('onAwaitingPassport', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public function onAwaitingCardDetails(Event $event)
    {
        $this->logger->debug('onAwaitingCardDetails', [
            'marking - places' => $event->getMarking()->getPlaces(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.leave' => 'onLeave',
            'workflow.customer_signup.transition' => 'onTransition',
            'workflow.customer_signup.entered' => 'onEntered',
            'workflow.customer_signup.enter' => [
                ['onEnter', 10],
//                ['onEnterDeclinedPassport', 20],
            ],
            'workflow.customer_signup.entered.awaiting_passport' => 'onAwaitingPassport',
            'workflow.customer_signup.entered.awaiting_card_details' => 'onAwaitingCardDetails',
            'workflow.customer_signup.entered.free_customer' => 'onFreeCustomer',
            'workflow.customer_signup.announce.add_passport' => 'onAnnounce',
            'workflow.customer_signup.announce.request_account_upgrade' => 'onAnnounceRAU',
            'workflow.customer_signup.announce.approve_vip' => 'onAnnounceAV',
            'workflow.customer_signup.announce.add_card_details' => 'onAnnounce',


            'workflow.customer_signup.leave' => array('onLeaveCustomerSignup'),
            'workflow.customer_signup.leave.prospect' => array('onLeaveCustomerSignupLeaveSignUp'),
        );
    }


    public function onLeaveCustomerSignup(Event $event)
    {
        $this->logger->info('Leaving in customer signup workflow');
    }

    public function onLeaveCustomerSignupLeaveSignUp(Event $event)
    {
        $this->logger->info('Leaving sign_up in customer signup workflow');
    }
}