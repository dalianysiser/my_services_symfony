<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceHistory;
use App\Form\ServiceHistoryType;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/editor/service')]
final class ServiceController extends AbstractController
{
    #[Route(name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if($image){
                $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $sluggerInterface->slug($name);
                $newname= $safe.'-'.uniqid().'.'.$image->getExtension();
                try {
                    $image->move(
                        $this->getParameter('image_dir'),
                        $newname
                    );
                } catch (FileException $exception) {}
                $service->setImage($newname);
            }
            $entityManager->persist($service);
            $entityManager->flush();

            $history = new ServiceHistory();
            $history->setQte($service->getStock());
            $history->setUser($this->getUser());
            $history->setService($service);
            $history->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($history);
            $entityManager->flush();

            $this->addFlash('success', 'Created!');
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if($image){
                $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $sluggerInterface->slug($name);
                $newname= $safe.'-'.uniqid().'.'.$image->getExtension();
                try {
                    $image->move(
                        $this->getParameter('image_dir'),
                        $newname
                    );
                } catch (FileException $exception) {dd($exception);}
                $service->setImage($newname);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Updated!');
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($service);
            $this->addFlash('danger', 'Deleted!');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update/stock/{id}/{cant}', name: 'app_service_update_stock', methods: ['POST','GET'])]
    public function updateStock($id, $cant, Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository): Response
    {
       $addStock = new ServiceHistory();
       $form = $this->createForm(ServiceHistoryType::class, $addStock);
       $form->handleRequest($request);
       $service = $serviceRepository->find($id);
       if($form->isSubmitted() && $form->isValid()){
            $newcant= $service->getStock() + $form->get('qte')->getData();;
            $service->setStock($newcant);
            $addStock->setUser($this->getUser());
            $addStock->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($addStock);
            $entityManager->flush();
            $this->addFlash('success', 'Created!');
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
       }
        return $this->render('service/addStock.html.twig', ['form'=>$form->createView(),'service'=>$service]);
    }
}
