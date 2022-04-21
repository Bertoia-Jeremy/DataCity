<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\PropertyValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/city")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/", name="city_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request, CityRepository $cityRepository): Response
    {

        $query = $cityRepository->selectWithFilter();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('city/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="city_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('city/new.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="city_show", methods={"GET"})
     */
    public function show(City $city): Response
    {
        $stats = $this->getPropertiesStats($city);

        return $this->render('city/show.html.twig', [
            'city' => $city,
            'stats' => $stats
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="city_delete", methods={"POST"})
     */
    public function delete(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param City $city
     * @return array[]
     * Permet de retourner toutes les stats voulues concernent les ventes d'une ville
     */
    private function getPropertiesStats(City $city): array
    {
        $properties = $city->getPropertyValues();

        $house = [];
        $local = [];
        $appartment = [];
        $buildingField = [];
        $stats = [];
        foreach ($properties as $property){
            $building_type = strtolower($property->getBuildingType());
            $surface = $property->getSurfaceBuilding()+$property->getSurfaceField();

            if($surface > 0){
                if($building_type === 'maison'){
                    $house[] = $property->getPrice()/($property->getSurfaceBuilding()+$property->getSurfaceField());
                }elseif($building_type === 'appartement'){
                    $appartment[] = $property->getPrice()/($property->getSurfaceBuilding()+$property->getSurfaceField());
                }elseif($building_type === 'local industriel. commercial ou assimilé'){
                    $local[] = $property->getPrice()/($property->getSurfaceBuilding()+$property->getSurfaceField());
                }elseif($property->getNatureCulture() === "terrains a bâtir"){
                    $buildingField[] = $property->getPrice()/($property->getSurfaceBuilding()+$property->getSurfaceField());
                }
            }

        }

        if(count($house) > 0){
            $stats["Maison"] = [
                "number" => count($house),
                "m2" => array_sum($house)/count($house)
            ];
        }
        if(count($buildingField) > 0){
            $stats["Terrain à bâtir"] = [
                "number" => count($buildingField),
                "m2" => array_sum($buildingField)/count($buildingField)
            ];
        }
        if(count($appartment) > 0){
            $stats["Appartement"] = [
                "number" => count($appartment),
                "m2" => array_sum($appartment)/count($appartment)
            ];
        }
        if(count($local) > 0){
            $stats["Local industriel. commercial ou assimilé"] = [
                "number" => count($local),
                "m2" => array_sum($local)/count($local)
            ];
        }
        return $stats;
    }
}
