<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PassportReviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:passport-review')
            ->addArgument('username', InputArgument::REQUIRED, 'the customer username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $io->writeln('Beginning passport approval process');

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
            if ($number < 7) {
                $workflow->apply($customer, 'automated_passport_approval');
                $io->text(sprintf('User "%s" was auto approved.', $username));
            } else {
                $workflow->apply($customer, 'require_manual_passport_approval');
                $io->warning(sprintf('User "%s" needs manual approval.', $username));
            }
        } catch (\LogicException $e) {
            $io->error(sprintf('Something went wrong: %s', $e->getMessage()));

            return false;
        }

        $em->flush();

        $io->success('Passport approval process completed.');
    }

}
