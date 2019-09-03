<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Restaurant as Restaurant;
use App\Entity\ResTables as RestaurantTable;

class RestaurantFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $restaurant1 = new Restaurant();
        $restaurant1->setTitle('Red Lobster')->
        setStatus(Restaurant::STATUS_ACTIVE)->setPhoto('Red-Lobster-Logo.png');

        $manager->persist($restaurant1);

        $restaurant1Table1 = new RestaurantTable();
        $restaurant1Table1->setRestaurant($restaurant1)->
          setNumber(1)->setCapacity(4)->setStatus(RestaurantTable::STATUS_ACTIVE);

        $manager->persist($restaurant1Table1);

        $restaurant1Table2 = new RestaurantTable();
        $restaurant1Table2->setRestaurant($restaurant1)->
          setNumber(2)->setCapacity(3)->setStatus(RestaurantTable::STATUS_ACTIVE);

        $manager->persist($restaurant1Table2);

        $restaurant1Table3 = new RestaurantTable();
        $restaurant1Table3->setRestaurant($restaurant1)->
          setNumber(3)->setCapacity(2)->setStatus(RestaurantTable::STATUS_INACTIVE);

        $manager->persist($restaurant1Table3);

        $restaurant2 = new Restaurant();
        $restaurant2->setTitle('Texas RoadHouse')->
        setStatus(Restaurant::STATUS_INACTIVE)->setPhoto('texas_roadhouse.jpg');

        $manager->persist($restaurant2);

        $restaurant2Table1 = new RestaurantTable();
        $restaurant2Table1->setRestaurant($restaurant2)->
          setNumber(1)->setCapacity(2)->setStatus(RestaurantTable::STATUS_INACTIVE);

        $manager->persist($restaurant2Table1);

        $restaurant2Table2 = new RestaurantTable();
        $restaurant2Table2->setRestaurant($restaurant2)->
          setNumber(2)->setCapacity(4)->setStatus(RestaurantTable::STATUS_INACTIVE);

        $manager->persist($restaurant2Table2);

        $restaurant2Table3 = new RestaurantTable();
        $restaurant2Table3->setRestaurant($restaurant2)->
          setNumber(3)->setCapacity(2)->setStatus(RestaurantTable::STATUS_ACTIVE);

        $manager->persist($restaurant2Table3);

        $restaurant3 = new Restaurant();
        $restaurant3->setTitle('Red Robin')->
        setStatus(Restaurant::STATUS_ACTIVE)->setPhoto('red_robin.jpg');

        $manager->persist($restaurant3);

        $restaurant3Table1 = new RestaurantTable();
        $restaurant3Table1->setRestaurant($restaurant3)->
          setNumber(1)->setCapacity(4)->setStatus(RestaurantTable::STATUS_INACTIVE);

        $manager->persist($restaurant3Table1);

        $restaurant3Table2 = new RestaurantTable();
        $restaurant3Table2->setRestaurant($restaurant3)->
          setNumber(2)->setCapacity(2)->setStatus(RestaurantTable::STATUS_ACTIVE);

        $manager->persist($restaurant3Table2);

        $restaurant3Table3 = new RestaurantTable();
        $restaurant3Table3->setRestaurant($restaurant3)->
          setNumber(3)->setCapacity(2)->setStatus(RestaurantTable::STATUS_ACTIVE);

        $manager->persist($restaurant3Table3);

        $manager->flush();
    }
}
