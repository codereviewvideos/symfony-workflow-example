<?php

namespace AppBundle\Event\Subscriber;

use AppBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Exception\LogicException;

class CustomerSignUpGuard implements EventSubscriberInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(FlashBagInterface $flashBag, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onTransitionRequest(GuardEvent $event)
    {
//        $this->flashBag->set('info', 'onTransitionRequest was called!');

        if ($this->authorizationChecker->isGranted('ROLE_USER') === false) {
            $event->setBlocked(true);
        }
    }

    public function guardAgainstBlockedCountries(GuardEvent $event)
    {
        $this->flashBag->set('info', 'guardAgainstBlockedCountries was called!');


        $blockedCountries = [
            'AQ'
        ];

        /**
         * @var $customer Customer
         */
        $customer = $event->getSubject();

        $customerIsInBlockedCountry = in_array(
            $customer->getCountry(),
            $blockedCountries,
            true
        );

        if ($customerIsInBlockedCountry === false) {
            return;
        }

        $event->setBlocked('true');

        throw new LogicException('bad country');
    }

    public static function getSubscribedEvents()
    {
        return [
            // workflow.guard
            // workflow.[workflow name].guard
            // workflow.customer_signup.guard
            // workflow.[workflow name].guard.[transition name]
            'workflow.customer_signup.guard' => 'onTransitionRequest',
            'workflow.customer_signup.guard.request_account_upgrade' => 'guardAgainstBlockedCountries'
        ];
    }
}