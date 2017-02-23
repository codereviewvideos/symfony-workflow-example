<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CardDetailsReviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:card-details-review')
            ->addArgument('username', InputArgument::REQUIRED, 'the customer username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $io->writeln('Beginning card details approval process');

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $customer = $em->getRepository('AppBundle:Customer')->findOneBy([
            'username' => $username
        ]);

        if ($customer === null) {
            $io->error(sprintf('Sorry, could not find the user "%s"', $username));

            return false;
        }

        $workflow = $this->getContainer()->get('workflow.customer_signup');

        $number = mt_rand(1,10);

        try {
            if ($number < 9) {
                $workflow->apply($customer, 'approve_card_details');
                $io->text(sprintf('User "%s" was approved.', $username));
            } else {
                $workflow->apply($customer, 'decline_card_details');
                $io->warning(sprintf('User "%s" was declined.', $username));
            }
        } catch (\LogicException $e) {
            $io->error(sprintf('Something went wrong: %s', $e->getMessage()));

            return false;
        }

        $em->flush();

        $io->success('Card details approval process completed.');
    }

}
