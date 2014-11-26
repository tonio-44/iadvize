<?php

namespace Iadvize\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Iadvize\BlogBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function showPostsAction(Request $request)
    {
    	$repository = $this->getDoctrine()->getRepository('IadvizeBlogBundle:Post');
		$qb = $repository->createQueryBuilder('p');
		
		if($author = $request->get('author')){
    		 $qb->andwhere('p.author = :author')
			->setParameter('author', $author);
    	}
		
		if($from = $request->get('from')){
    		 $qb->andwhere($qb->expr()->gte('p.date', '2014-11-22'));
    	}
		
		if($to = $request->get('to')){
    		 $qb->andwhere($qb->expr()->lte('p.date', '2014-11-30'));
    	}
		
		
		

		
		    
			
			/*
			->where('p.author = :price');
		    ->setParameter('price', '19.99')
		    ->orderBy('p.price', 'ASC')
		    ->getQuery();
		*/
    	$posts = $qb->getQuery()->getResult();
		$nb = 0;
		foreach($posts as $post){
			echo "$nb - " . $post->getContent().'<br/>';
			$nb++;
		}
			$name = "tonio";
			return $this->render(
								'IadvizeBlogBundle:Default:index.html.twig', 
								 array('name' => $name));
	}


	public function createAction(){
		$product = new Post();
	    $product->setContent('A Foo Bar');
	    $product->setAuthor('Tonio');
	    $product->setDate(new \DateTime('25-12-2014'));
	
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($product);
	    $em->flush();
	
	    return new Response('Id du produit créé : '.$product->getId());
	}
}
