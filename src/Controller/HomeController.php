<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    //TODO - 1 - Pouvoir chercher par nom  de ville
    //TODO - 2 - Commencer à faire une petite mise en page du site (centrer les titres, avoir quelque chose qui parte pas dans tous les sens
    //TODO - (optimisation) - Changer le format de fichier de gz en csv pour vérifier si ça va + vite, chronométrer pour re-tester les 2 insert sql/dql
    //TODO - (pas maintenant) - Consolider les données par ville, département, région puis par année pour chaque
    //TODO - (plus tard) - Afficher les données sous forme de couleur, simplifier la visualisation des données au max
    //TODO - Nettoyer les données intégrées en BDD, celles reliées à aucune ville, faire un tri dessus pour les associer correctement
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
