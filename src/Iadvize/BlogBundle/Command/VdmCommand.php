<?php
// src/Iadvize/BlogBundle/Command/VdmCommand.php
namespace Iadvize\BlogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Iadvize\BlogBundle\Component\VdmSiteCrawler;
use Doctrine\ORM\EntityManager;
use Iadvize\BlogBundle\Entity\Post;
use Symfony\Component\Console\Helper\ProgressBar;

class VdmCommand extends ContainerAwareCommand
{
	protected function configure()
    {
        $this	->setName('VDM:load')
		
				->setDescription('Permettre  à  l’aide  d’une  ligne  de  commande,  d’aller  chercher  les  200  derniers  enregistrements  du  site  "Vie  de  
merde"1   et  de  les  stocker.  (Champs  à  récupérer  :  Contenu,  Date  et  heure,  et  auteur)');

    }
	
	protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$dialog = $this->getHelperSet()->get('dialog');
		
		$g_progress = new ProgressBar($output, 3);
		
		$g_progress->setFormat('%message% %current%/%max% [%bar%] %percent:3s%%');
		
		$em =$this->getApplication()->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
				
		$g_progress->setMessage("<fg=green>Base de données : </fg=green>\r\n");
		
		$g_progress->start();
		
		$output->writeln('');
		
		$truncate = true;
		
		//
		//	Etape 1 : doit-on vider la table des posts ?
		//
		if (!$dialog->askConfirmation( $output, '<question>- Vider la table des VDM (yes/no)?</question>', true ) == true)
		{
    			
    		$truncate = false;
			
		}
		if($truncate){// Si oui, on utilise EntityManager pour vider la table à l'aide d'une requête
			
			$em->createQuery('DELETE FROM Iadvize\BlogBundle\Entity\Post')->execute();
			
			$output->writeln('');
			
			$output->writeln('<fg=green>La table des posts a été effacée</fg=green>');
			
			$output->writeln('');
		}

		$output->writeln('');
		
		//
		//	 Etape 2 : On récupère les posts sur le site VDM à l'aide du service iadvize site crawler
		//
		
		$g_progress->setMessage('<fg=green>lecture des données sur le site VDM : </fg=green>');
		
		$g_progress->advance();
		
        $vdm = $this->getContainer()->get('iadvize_site_crawler');// accéder au service...
                
		$html_posts = $vdm->load();// lire les posts en ligne
				
		$g_progress->setMessage('<fg=green>Extraction des données du site : </fg=green>');
		
		$g_progress->advance();
		
		$progress = $this->getHelperSet()->get('progress');
		
		//
		//	lecture du tableau renvoyé par la fonction load du service
		//
		foreach ($html_posts as $html_post) {
			
			$raw_posts[] = $vdm->extractVdmPost($html_post);
		}
		
		$output->writeln('');
		
		$g_progress->setMessage('<fg=green>Sauvegarde des données : </fg=green>');
		
		$g_progress->advance();
		
		$progress->start($output, count($raw_posts));
		
		//
		//
		// Etape 3: sauvegarde des données
		
		foreach ($raw_posts as $raw_post) {
			
			$post = new Post();
			
			$post -> setContent($raw_post['content']);
			
			$post -> setAuthor($raw_post['author']);
			
			$date = new \DateTime($raw_post['date']);
			
			$post -> setDate($date);
			
			$progress->advance();
			
			$em -> persist($post);
			
			$em->flush();
		}
		
		$progress->clear();
		
		$g_progress->display();
		
		$g_progress->setMessage('<fg=green>Enregistrement des informations terminée : </fg=green>');
		
		$g_progress->finish();

		$output->writeln('');
		
		$output->writeln('<fg=green>FIN du traitement VDM</fg=green>');
		
		$output->writeln('');
    }
}