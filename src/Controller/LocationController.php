<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location-dummy')]
class LocationController extends AbstractController
{
    #[Route('/', name: 'location')]
    public function index(LocationRepository $repo): JsonResponse
    {
        $locations = $repo->findAllWithForecasts();
        
        $json = [];
        foreach ($locations as $location) {
            $locationJson[] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'country' => $location->getCountryCode(),
                'lat' => $location->getLatitude(),
                'long' => $location->getLongitude()
            ];

            foreach ($location->getForecasts() as $forecast) {
                $locationJson['forecast'][$forecast->getDate()->format('Y-m-d')] = [
                    'celsius' => $forecast->getCelsius()
                ];
            }
            $json[] = $locationJson;

        }
        return new JsonResponse($json);
    }

    #[Route('/create', name:'create-location', methods:['GET', 'POST'])]
    public function create(EntityManagerInterface $em): JsonResponse
    {
        $location = new Location();
        // tell Doctrine to be aware of the Location instance
        $em->persist($location);
        $location->setName('Batangas')
            ->setCountryCode('PH')
            ->setLatitude(13.4521)
            ->setLongitude(4.5528); 
        
        // save to database

        $em->flush();

        return new JsonResponse([
            'id' => $location->getId() //Doctrine knows the Id of the last inserted record
        ]);

    }

    #[Route('/save', name:'save-location', methods: ['GET', 'POST'])]
    public function saveLocation(LocationRepository $repo): Response
    {
        $location = new Location();
        $location->setName('Alitagtag')->setCountryCode('PH')->setLongitude(45.2147)->setLatitude(78.2569);
        $repo->save($location, true);
        return $this->redirectToRoute('list-locations');
    }

    #[Route('/list-locations', name:'list-locations', methods:['GET'])]
    public function listAllLocations(LocationRepository $lr): Response {
        $location = new Location();
        $list = $lr->findAll();
        
        $templateData = [];
        foreach($list as $loc) {
            $templateData[] = [
                'id' => $loc->getId(),
                'city' => $loc->getName(),
                'country_code' => $loc->getCountryCode()
            ];
        }
        return $this->render('location/list.html.twig', [
            'list' => $templateData
        ]);

    }

    #[Route('/edit', name:'edit-location', methods:['GET'])]
    public function editLocation(LocationRepository $locationRepository, Request $request, EntityManagerInterface $em): JsonResponse 
    {
        $locationId = $request->query->get('location-id');
        $location = $locationRepository->find($locationId);
        $locName = $location->getName();
        $locName .= '-edited';
        $location->setName($locName);

        //since using the Repository, we do not need to call persist() method of the EntityManagerInterface
        // because the EntityManager is already aware of the object instance - Location
        $em->flush();

        return new JsonResponse([
            'id' => $location->getId(),
            'name' => $location->getName()
        ]);

    }

    #[Route('/remove/{id}', name:'remove-location', methods: ['POST', 'GET'])]
    public function remove(LocationRepository $repo, $id): Response
    {
        $location = $repo->find($id);
        $repo->remove($location, true);

        $this->addFlash('success', 'Location has been removed');
        return $this->redirectToRoute('list-locations');
    }

    #[Route('/show/{name}', name:'show-location', methods:['GET', 'POST'])]
    public function showLocation($name, Request $request, LocationRepository $repo): JsonResponse
    {
        $location = $repo->findOneByName($name);
        if(!$location) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse([
            'id' => $location->getId(),
            'name' => $location->getName(),
            'country_code' => $location->getCountryCode(),
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude()
        ]);
    }

    #[Route('/{name}/show', name:'id-location', methods: ['GET', 'POST'])]
    public function show(Location $location): JsonResponse    
    {
        $json = [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'country_code' => $location->getCountryCode(),
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude()
        ];
        foreach ($location->getForecasts() as $forecast) {
            $json['forecast'][$forecast->getDate()->format('Y-m-d')] = [
                'celsius' => $forecast->getCelsius()
            ];
        }
        return new JsonResponse($json);
    }


}

