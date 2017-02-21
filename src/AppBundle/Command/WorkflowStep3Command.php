<?php

namespace AppBundle\Command;

use AppBundle\Entity\Customer;
use AppBundle\Entity\SalesLead;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowStep3Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('workflow:step-3')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $salesLead = $em->getRepository('AppBundle:SalesLead')->findAll();
        $salesLead = array_pop($salesLead);

        $workflow = $this->getContainer()->get('workflow.sales_lead');
        $workflow->apply($salesLead, 'converted_from_sales_call');

        $em->flush();

        $output->writeln('Command result.');
    }

}
