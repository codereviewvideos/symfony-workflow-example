<?php

namespace AppBundle\Event\Subscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class CustomAuditTrailListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLeave(Event $event)
    {
        foreach ($event->getTransition()->getFroms() as $place) {
            $this->logger->info(sprintf('Leaving "%s" for subject of class "%s".', $place, get_class($event->getSubject())));
        }
    }

    public function onLeaveCustomerSignup(Event $event)
    {
        $this->logger->info('Leaving in customer signup workflow');
    }

    public function onLeaveCustomerSignupLeaveSignUp(Event $event)
    {
        $this->logger->info('Leaving sign_up in customer signup workflow');
    }


    public function onTransition(Event $event)
    {
        $this->logger->info(sprintf('Transition "%s" for subject of class "%s".', $event->getTransition()->getName(), get_class($event->getSubject())));
    }

    public function onEnter(Event $event)
    {
        foreach ($event->getTransition()->getTos() as $place) {
            $this->logger->info(sprintf('Entering "%s" for subject of class "%s".', $place, get_class($event->getSubject())));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.leave' => array('onLeave'),
            'workflow.customer_signup.leave' => array('onLeaveCustomerSignup'),
            'workflow.customer_signup.leave.sign_up' => array('onLeaveCustomerSignupLeaveSignUp'),
            'workflow.transition' => array('onTransition'),
            'workflow.enter' => array('onEnter'),
        );
    }
}
