<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use  App\Repository\ServiceRepository;

class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['GET'])]
    public function index(Request $request, ServiceRepository $service, PaginatorInterface $paginatorInterface): Response
    {
        if ($request->isMethod('GET')) {
            $data = $request->query->all();
            $word = $data['word'];
            if($word == '' || $word == null){
                $result= $service->findBy([],['id'=>'DESC']);
            }else  $result = $service->searchEngine($word);
            
            $data = $paginatorInterface->paginate(
                $result,
                $request->query->getInt('page', 1),
                12
            );
        }
        return $this->render('search_engine/index.html.twig', [
            'services' => $data,
            'word'=>$word
        ]);
    }
}
