<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    //TODO - 1 - Pouvoir chercher par nom  de ville
    //TODO - 2 - Commencer � faire une petite mise en page du site (centrer les titres, avoir quelque chose qui parte pas dans tous les sens
    //TODO - (optimisation) - Changer le format de fichier de gz en csv pour v�rifier si �a va + vite, chronom�trer pour re-tester les 2 insert sql/dql
    //TODO - (pas maintenant) - Consolider les donn�es par ville, d�partement, r�gion puis par ann�e pour chaque
    //TODO - (plus tard) - Afficher les donn�es sous forme de couleur, simplifier la visualisation des donn�es au max
    //TODO - Nettoyer les donn�es int�gr�es en BDD, celles reli�es � aucune ville, faire un tri dessus pour les associer correctement
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
