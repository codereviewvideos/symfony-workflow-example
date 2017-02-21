<?php

namespace AppBundle\Command;

use AppBundle\Entity\Customer;
use AppBundle\Entity\SalesLead;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowStep1Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('workflow:step-1')
            ->addArgument('name', InputArgument::REQUIRED, 'the customer name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $customer = $em->getRepository('AppBundle:Customer')->findOneBy([
            'name' => $input->getArgument('name')
        ]);

        if ($customer === null) {
            $customer = new Customer($input->getArgument('name'));
            $em->persist($customer);
        }

        $salesLead = (new SalesLead())
            ->setCustomer($customer)
//            ->setStage(['capture_sales_lead'=>1])
            ->setNotes('added some notes about Bob')
        ;

        $em->persist($salesLead);

        $em->flush();

        $output->writeln('Command result.');
    }

}
