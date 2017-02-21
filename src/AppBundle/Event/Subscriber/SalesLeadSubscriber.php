<?php

namespace AppBundle\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Workflow;

class SalesLeadSubscriber implements EventSubscriberInterface
{
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function onLeave(Event $event)
    {
//        exit(\Doctrine\Common\Util\Debug::dump($event));
    }

//    public function onTransition(Event $event)
//    {
//        exit(\Doctrine\Common\Util\Debug::dump($event));
//    }
//
//    public function onEnter(Event $event)
//    {
//        exit(\Doctrine\Common\Util\Debug::dump($event));
//    }
    public function onAnnounce(Event $event)
    {
        $this->workflow->apply($event->getSubject(), 'converted_from_thank_you_email');
    }
//    public function onTransitionJournalist(GuardEvent $event)
//    {
//        if (!$this->checker->isGranted('ROLE_JOURNALIST')) {
//            $event->setBlocked(true);
//        }
//    }
//
//    public function onTransitionSpellChecker(GuardEvent $event)
//    {
//        if (!$this->checker->isGranted('ROLE_SPELLCHECKER')) {
//            $event->setBlocked(true);
//        }
//    }

    public static function getSubscribedEvents()
    {
        return array(
//            'workflow.sales_lead.leave' => 'onLeave',
//            'workflow.sales_lead.transition' => 'onTransition',
//            'workflow.sales_lead.enter' => 'onEnter',
            'workflow.sales_lead.announce.converted_from_thank_you_email' => 'onAnnounce',
        );
    }
}
