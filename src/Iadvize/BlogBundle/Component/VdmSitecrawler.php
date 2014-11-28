<?php
//src: Iadvize\BlogBundle\Component\Sitecrawler
namespace Iadvize\BlogBundle\Component;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DomCrawler\Crawler;
use Iadvize\BlogBundle\Entity\Post;

/**
 * A console command for dumping post at vdm.fr.
 *
 * @author Kébert Anthony <atouts-web@orange.fr>
 */

class VdmSitecrawler {
	/**
	 * url de la recherche
	 *
	 * @__host string
	 */
	private $__host;
	/**
	 * gestionnaire de session
	 *
	 * @__curl ressource
	 */
	private $__curl;
	/**
	 * gestionnaire de session
	 *
	 * @__limite nombre de posts à lire
	 */
	private $__limit = 200;
	/**
	 * 
	 * Doctrine Manager injecté par le fichier de config (BlogBundle/Ressources/config/config.ym)
	 *
	 * @__limite nombre de posts à lire
	 */
	private $__dm = null;
	

	public function __construct($host, $limit) {
		
		// initialisation du service avec les paramètres définis dans /BlogBundle/Ressources/config/
		$this -> __host = $host;
		
		$this -> __limit = $limit;
		
		$this -> init();
	}

	public function init() {
		
		$this -> __curl = curl_init();
		
		curl_setopt($this -> __curl, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($this -> __curl, CURLOPT_FOLLOWLOCATION, true);
		
		curl_setopt($this -> __curl, CURLOPT_HTTPHEADER, array("Accept: text/xml,application/xml,application/xhtml+xml,"));
		
		curl_setopt($this -> __curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
		
		return $this;
	}

	/**
	 * 
	 * return : un tableau d'objets DomElement (posts extraits du site VDM au format html)
	 */
	private function getHtmlPosts(){
		
		$fetch = true;//drapeau d'arrêt du traitement
		
		$html_posts = [];//tableau des éléments
		
		$index = $page = 0;//algorithme
		
		while ($fetch) {
			
			$crawler = $this -> getPage($page);//lire une page...
			
			foreach ($crawler as $domElement) {//puis pour chaque post...
				
				$html_posts[] = $domElement;//l'ajouter au tableau de sortie de la fonction
				
				$index++;
				
				if ($index >= $this -> __limit) {//limite de 200 posts (limite définie dans config.yml du Bundle)
					
					$fetch = false;
				
					break;
				}
			}
			$page++;
		}
		
		$this->stop();
		
		return $html_posts;
	}
	/**
	 *
	 * @return : un tableau composé d'éléments HTML (DomElement)
	 */
	public function load() {
		
		return $this->getHtmlPosts();
		
	}

  /**
  * extractVdmPost $html_post : DomElement
  * @return : un tableau contenant les proriétés d'un Post à partir d'un DomElement 
  */
	public function extractVdmPost($html_post){
		
		$author = '';
		
		$html_post_crawler = new Crawler($html_post);

		$id = $html_post_crawler -> attr('id');//Id du post
		
		//contenu de la première balise <p> rencontrée
		$content = ($html_post_crawler -> filter('p') -> first() -> text());//texte du post
		
		//	on analyse la 2ième balise <p> dans : 
		//	<div class="date">
		//		<div class="right_part">
		//			<p>
		//			<p> <=====
		$p = $html_post_crawler -> filter('div.date > div.right_part p') -> eq(1) -> text();
		
		if (preg_match('/(?P<month>\d{2})\/(?P<day>\d{2})\/(?P<year>\d{4})/', $p, $regs)) {
			
			$date = $regs['month'] . '-' . $regs['day'] . '-' . $regs['year'];
			
		}
		if (preg_match('/(?P<hour>\d{2}):(?P<minutes>\d{2})/', $p, $regs)) {
			
			$datetime = $date . ' ' . $regs['hour'] . ":" . $regs['minutes'];
			
		}
		if (preg_match('/par (?P<name>\w+)/', $p, $regs)) {
			
			$author = $regs['name'];
			
		}
		
		return array(	'id' 		=> $id,
		
						'author' 	=> $author,
						
						'date'	=> $datetime,
						
						'content'	=> $content
		);
	}

	/**
	 *
	 */
	public function getPage($n = 0) {
		
		$url = $this -> __host;
		
		$url = $url . "/?page=" . $n;
		
		curl_setopt($this -> __curl, CURLOPT_URL, $url);
		
		$html = curl_exec($this -> __curl);
		
		$crawler = new Crawler($html);
		
		//utilisation du composant Crawler de Symfony
		//liste des posts (balise div contenant la classe article sur la page récupérée)
		//filtre avec CssSelector
		return $crawler -> filter('div.article');
	}
	
	
	/**
	 * stop the url session
	 */
	public function stop() {
		
		curl_close($this -> __curl);
		
	}

}
