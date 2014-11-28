<?php

namespace Iadvize\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Iadvize\BlogBundle\Entity\Post;


class ApiControllerTest extends WebTestCase
{
	
	/**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
		
        $this->em = static::$kernel->getContainer()
		
            ->get('doctrine')
			
            ->getManager()
        ;
    }
	
    public function testIndex()
    {
    	
		$client = static::createClient();

        $client->request('GET', '/api/posts');
		
		$html =  $client->getResponse()->getContent();
		
		$crawler = new Crawler($html);

        $this->assertTrue($crawler->filter('body:contains("author")')->count() > 0);
    }
	
	public function testParameterAuthorFromTo(){
		
		$client = static::createClient();

        $client->request('GET', '/api/posts?author=Anonyme&from=2014-10-01&to=2015-01-01');
		
		$html =  $client->getResponse()->getContent();
		
		$crawler = new Crawler($html);

        $this->assertTrue($crawler->filter("body:contains(\"author\"\:\"Anonyme\")")->count() > 0);
	}
	
	public function testById(){
		
		$posts = $this->em->getRepository('IadvizeBlogBundle:Post')->findAll();

        $this->assertTrue(count($posts) > 0);
		
		$post = $posts[0];
		
		$id = $post->getId();
		
		$author = $post->getAuthor();
		
		echo "Searching ID : " . $id . "for author : ". $author;
		
		$client = static::createClient();

        $client->request('GET', '/api/posts/' . $id);
		
		$html =  $client->getResponse()->getContent();
		
		$crawler = new Crawler($html);

        $this->assertTrue($crawler->filter("body:contains(\"author\"\:\"" . $author . "\")")->count() > 0);
	}
}
