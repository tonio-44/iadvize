<?php
//src: Iadvize\BlogBundle\Component\Sitecrawler
namespace Iadvize\BlogBundle\Component;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DomCrawler\Crawler;
use Iadvize\BlogBundle\Entity\Post;

class VdmSitecrawler{
	
	private $__host;
	private $__page	= 0;//index de la page (page d'accueil)
	private $__header;
	private $__curl;
	private $__limit 	= 200;//nombre de posts à lire
	private $__index	= 0;//index du dernier article lu sur la page en cours)
	private $__dm 		= null;//Doctrine Manager goes here
	
	public function __construct($host, $limit, EntityManager $dm)
    {
    	// utilisation du service siteCrawler
    	// initialisation du service avec les paramètres définis dans /BlogBundle/Ressources/config/
        $this->__host = $host;
		$this->__limit = $limit;
		$this->__dm = $dm;
		$this->init();
    }
	
	public function init(){
		$this->__curl = curl_init();
		$this->__header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$this->__header[] = "charset=utf-8";
		curl_setopt($this->__curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->__curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->__curl, CURLOPT_HTTPHEADER,$this->__header);
		curl_setopt($this->__curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
		return $this;
	}
	
	/**
	 * 
	 */
	public function load(){
		while($this->__index < $this->__limit){//tant qu'on a pas atteind les 200 posts
		
			$crawler = $this->getPage($this->__page);//lecture de la page d'accueil
			
			//liste des posts (balise div contenant la classe article sur la page récupérée)
			$posts = $crawler->filter('div.article');//filtre avec CssSelector
			
			
			$array_post;
			foreach($posts as $post){
				$array_post[] = $post;
				$this->__index++;
			}
			$this->__page++;
			
			foreach($array_post as $html_post){
				$html_post_crawler = new Crawler($html_post);
				
				$id = $html_post_crawler->attr('id');
				$content = $html_post_crawler->filter('p')->first()->text();
					
				$p = $html_post_crawler->filter('div.date > div.right_part p')->eq(1)->text();
				if (preg_match('/(?P<month>\d{2})\/(?P<day>\d{2})\/(?P<year>\d{4})/', $p, $regs)) {
						$date = $regs['month'] . '-' . $regs['day'] . '-' . $regs['year'];
				}
				if (preg_match ('/(?P<hour>\d{2}):(?P<minutes>\d{2})/', $p, $regs)) {
						$time = $regs['hour'] . ":" . $regs['minutes'];
				}
				if (preg_match ('/par (?P<name>\w+)/', $p, $regs)) {
						$author = $regs['name'];
				}
				$post = new Post();
				$post->setContent($content);
				$post->setAuthor($author);
				$date = new \DateTime($date);
				$post->setDate($date);
			    $this->__dm->persist($post);
			    $this->__dm->flush();
			}
		}
	}
	
	private function getPage($n = 0){
		$url = $this->__host;
		if($n){
			$url = $url . "/?page=" . $n;
		}
		curl_setopt($this->__curl, CURLOPT_URL, $url);
		$html = curl_exec($this->__curl);
		return new Crawler($html);//utilisation du composant Crawler de Symfony
	}
	
	public function stop(){
		curl_close($this->__curl);
	}
	
}
