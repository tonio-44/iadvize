<?php

namespace Iadvize\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Iadvize\BlogBundle\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiController extends Controller
{
	/**
	 * 
	 *
	 */
    public function showPostsAction(Request $request)
    {
    	$repository = $this->getDoctrine()->getRepository('IadvizeBlogBundle:Post');
		
		$results = $repository->getPostsFromRequestParameters($request);
		
		foreach($results as $post){
			
			$content =  $post->getContent();
			
			$datasResult[] = array(	'id' => $post->getId(),
			
									'content' => $content,
									
									'author' => $post->getAuthor(),
									
									'date' => $post->getDate()->format('Y-m-d H:i')
									
			);
		}
		
		$response = new JsonResponse();
		
		$response->headers->set('Content-Type', 'application/json');
		
		$response->setData(array(
		
		    'posts' => $datasResult,
		    
		    'count' => count($datasResult)
			
		));
		
		return $response;
	}


	public function showAction($id){
		
		$repository = $this->getDoctrine()->getRepository('IadvizeBlogBundle:Post');
		
		$post = $repository->findOneById($id);
		
		$response = new JsonResponse();
		
		$response->headers->set('Content-Type', 'application/json');
		
		$response->setData(array(
		
		  		'id' => $post->getId(),
			
				'content' => $post->getContent(),
				
				'author' => $post->getAuthor(),
				
				'date' => $post->getDate()
			
		));
		
		return $response;
		
	}
}
