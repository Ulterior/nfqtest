<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Restaurant;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT COUNT(r) FROM App:".$em->getClassMetadata(Restaurant::class)->getTableName()." r");

        $restaurantCount = $query->getSingleScalarResult();

        return $this->render('home/index.html.twig', [
            'restaurant_count' => $restaurantCount,
        ]);
    }
}
