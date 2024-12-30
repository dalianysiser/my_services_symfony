<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ServiceRepository $serviceRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        
        $data= $serviceRepository->findBy([],['id'=>'DESC']);
        $services = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('home/index.html.twig', [
            'services' => $services,
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route('/home/service/{id}/show', name: 'app_home_service_show')]
    public function show(Service $service, ServiceRepository $serviceRepository, CategoryRepository $categoryRepository): Response
    {
        $lastProduct = $serviceRepository->findBy([],['id'=>'DESC'], 5);
        return $this->render('home/show.html.twig', [
            'service' => $service,
            'services' => $lastProduct,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/home/service/subCategory/{id}/filter', name: 'app_home_service_filter')]
    public function filter($id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository): Response
    {
        $serviceS = $subCategoryRepository->find($id)->getServices();
        $subCategory = $subCategoryRepository->find($id);
        return $this->render('home/filter.html.twig', [
            'services' => $serviceS,
            'subCategory'=>$subCategory,
            'categories'=>$categoryRepository->findAll()
        ]);
    }
}
