<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServiceHistoryRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ServiceHistoryController extends AbstractController
{
    #[Route('/service/history', name: 'app_service_history')]
    public function index(ServiceHistoryRepository $serviceHRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        $data= $serviceHRepository->findBy([],['id'=>'ASC']);
        $servicesH = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('service_history/index.html.twig', [
            'servicesH' => $servicesH,
            'categories' => $categoryRepository->findAll()
        ]);
    }
}
