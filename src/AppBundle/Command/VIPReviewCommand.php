<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VIPReviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:vip')
            ->addArgument('username', InputArgument::REQUIRED, 'the customer username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $io->writeln('Giving the VIP treatment');

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $customer = $em->getRepository('AppBundle:Customer')->findOneBy([
            'username' => $username
        ]);

        if ($customer === null) {
            $io->error(sprintf('Sorry, could not find the user "%s"', $username));

            return false;
        }

        $workflow = $this->getContainer()->get('workflow.customer_signup');

        try {

            $workflow->apply($customer, 'approve_vip');
            $io->text(sprintf('User "%s" was VIP approved.', $username));

        } catch (\LogicException $e) {
            $io->error(sprintf('Something went wrong: %s', $e->getMessage()));

            return false;
        }

        $em->flush();

        $io->success('VIP treatment complete');
    }

}
