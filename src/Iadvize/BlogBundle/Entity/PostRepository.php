<?php

namespace Iadvize\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
	public function getPostsFromRequestParameters(Request $req){
		
		$qb = $this->createQueryBuilder('p');
		
		if($author = $req->get('author')){
			
    		 $qb->andwhere('p.author LIKE :author')
			 
			->setParameter('author', $author);
    	}
		
		if($from = $req->get('from')){
			
    		 $qb->andWhere('p.date >= :from')->setParameter('from', new \DateTime($from));
    	}
		
		if($to = $req->get('to')){
			
    		 $qb->andWhere('p.date <= :to')->setParameter('to', new \DateTime($to));
    	}
		
		return $qb->getQuery()->getResult();
	}
	
}
