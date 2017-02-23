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

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.customer_signup.leave' => 'onLeave',
            'workflow.customer_signup.transition' => 'onTransition',
            'workflow.customer_signup.enter' => [
                ['onEnter', 10],
                ['onEnterDeclinedPassport', 20],
            ],
            'workflow.customer_signup.announce.decline_passport' => 'onAnnounceDeclined',
        );
    }
}
