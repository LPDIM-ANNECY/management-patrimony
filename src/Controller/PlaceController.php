<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }


    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(Place::class);
        $places = $repository->findAll();
        return $this->render('place/index.html.twig', [
            'controller_name' => 'PlaceController',
            'places' => $places
        ]);
    }

    #[Route('/add', name: 'add')]
    public function addPlace(Request $request){
        $form = $this->createForm(PlaceType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_USER')) {
            $place = $form->getData();
            $place->setUpdateAt(new \DateTime('now'));

            $this->entityManager->persist($place);
            $this->entityManager->flush();

            $this->addFlash('success', 'Place crée');
            return $this->redirectToRoute('place_read', ["id" => $place->getId()]);
        }

        return $this->render('place/add.html.twig', ['controller_name' => 'PlaceController',
            'form' => $form->createView()]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deletePlace(Place $place){
        $toDeletePlace = $this->entityManager->getRepository(Place::class)->find($place->getId());

        $this->entityManager->remove($toDeletePlace);
        $this->entityManager->flush();

        return $this->redirect("/place");
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function editPlace(Place $place, Request $request): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_USER')) {
            $formPlace = $form->getData();

            $this->entityManager->persist($formPlace);
            $this->entityManager->flush();

            $this->addFlash('success', 'Place mise à jour');
            return $this->redirectToRoute('place_read', ["id" => $formPlace->getId()]);
        }

        return $this->render('place/edit.html.twig', ['controller_name' => 'PlaceController',
            'form' => $form->createView(),
            'place' => $place]);
    }

    #[Route('/{id}', name: 'read')]
    public function getPlace(Place $place){
        return $this->render('place/place.html.twig', [
            'controller_name' => 'PlaceController',
            'place' => $place
        ]);
    }
}
