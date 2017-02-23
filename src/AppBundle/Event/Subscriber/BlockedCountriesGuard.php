<?php

namespace AppBundle\Event\Subscriber;

use AppBundle\Entity\Customer;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class BlockedCountriesGuard implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * BlockedCountriesGuard constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, FlashBagInterface $flashBag)
    {
        $this->logger = $logger;
        $this->flashBag = $flashBag;
    }

//    public function onTransition(GuardEvent $event)
//    {
//        // For all action, user should be logger
//        if (!$this->checker->isGranted('IS_AUTHENTICATED_FULLY')) {
//            $event->setBlocked(true);
//        }
//    }

    public function onGuardBlockedCountries(GuardEvent $event)
    {
        $this->logger->debug('onGuardTransitionBlockedCountries', [
            'places' => $event->getMarking()->getPlaces(),
            'transname' => $event->getTransition()->getName(),
        ]);

        if ($event->isBlocked()) {
            return;
        }

        $blockedCountries = [ // injected, or whatever
            'AQ' // Antarctica
        ];

        /**
         * @var $customer Customer
         */
        $customer = $event->getSubject();

        $customerInBlockedCountry = in_array($customer->getCountry(), $blockedCountries, true);

        if ($customerInBlockedCountry === false) {
            return;
        }

//        $event->setBlocked(true);

//        $this->flashBag->add('danger', 'Sorry, this country is blocked');
    }

//    public function onTransitionSpellChecker(GuardEvent $event)
//    {
//        if (!$this->checker->isGranted('ROLE_SPELLCHECKER')) {
//            $event->setBlocked(true);
//        }
//    }

    public static function getSubscribedEvents()
    {
        return array(
//            'workflow.article.guard' => 'onTransition',
            'workflow.customer_signup.guard.request_account_upgrade' => 'onGuardBlockedCountries',
//            'workflow.article.guard.spellchecker_approval' => 'onTransitionSpellChecker',
        );
    }
}
