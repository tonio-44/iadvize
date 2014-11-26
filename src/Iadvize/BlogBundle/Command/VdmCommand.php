<?php
// src/Iadvize/BlogBundle/Command/VdmCommand.php
namespace Iadvize\BlogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Iadvize\BlogBundle\Component\VdmSiteCrawler;

class VdmCommand extends ContainerAwareCommand
{
	protected function configure()
    {
        $this
            ->setName('VDM:load')
            ->setDescription('Permettre  à  l’aide  d’une  ligne  de  commande,  d’aller  chercher  les  200  derniers  enregistrements  du  site  "Vie  de  
merde"1   et  de  les  stocker.  (Champs  à  récupérer  :  Contenu,  Date  et  heure,  et  auteur)')
            //->addArgument('name', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }
	
	protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vdm = $this->getContainer()->get('iadvize_site_crawler');
		$vdm->load();
        //$output->writeln($text);
    }
}