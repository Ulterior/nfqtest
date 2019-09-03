<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\ResTables as RestaurantTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class RestaurantTablesController extends AbstractController
{
    private $csrfTokenManager;
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/restables/{restaurant_id}", name="restaurant_tables", methods={"GET"})
     */
    public function index($restaurant_id)
    {
        $restaurant = $this->getDoctrine()
          ->getRepository(Restaurant::class)
          ->findOneById($restaurant_id);

        return $this->render('restaurant_tables/index.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/restables/{restaurant_id}/new", name="new_restaurant_table", methods={"GET"})
     */
    public function getNewRestaurantTableView($restaurant_id)
    {
        $restaurant = $this->getDoctrine()
          ->getRepository(Restaurant::class)
          ->findOneById($restaurant_id);

        return $this->render('restaurant_tables/create.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/restables/{restaurant_id}/table/{id}", name="edit_restaurant_table", methods={"GET"})
     */
    public function getRestaurantTableView($restaurant_id, $id)
    {
        $restaurant_table = $this->getDoctrine()
            ->getRepository(RestaurantTable::class)
            ->findOneById($id);

        return $this->render('restaurant_tables/edit.html.twig', [
            'table' => $restaurant_table
        ]);
    }

    /**
     * @Route("/restables/{restaurant_id}/remove/{id}", name="remove_restaurant_table")
     */
    public function removeRestaurantTable(Request $request, $restaurant_id, $id)
    {
        $restaurant_table = $this->getDoctrine()
          ->getRepository(RestaurantTable::class)
          ->findOneById($id);

        if (!isset($restaurant_table)) {
            return $this->render('restaurant_tables/edit.html.twig', [
                'error' => 'Invalid Record'
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($restaurant_table);
        $em->flush();

        return new RedirectResponse($this->urlGenerator->generate('restaurant_tables', [
          'restaurant_id' => $restaurant_id
        ]));
    }

    /**
     * @Route("/restables/{restaurant_id}/update/{id}", name="update_restaurant_table", methods={"POST"})
     */
    public function updateRestaurantTable(Request $request, $restaurant_id, $id)
    {
        $rparams = [
            'number' => $request->request->get('table_number'),
            'capacity' => $request->request->get('table_capacity'),
            'status' => $request->request->get('status'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('update_restaurant_table', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $restaurant_table = $this->getDoctrine()
            ->getRepository(RestaurantTable::class)
            ->findOneById($id);

        if (!isset($restaurant_table)) {
            return $this->render('restaurant_tables/edit.html.twig', [
                'error' => 'Invalid Record'
            ]);
        }

        $em = $this->getDoctrine()->getManager();

        $check_dup_number = $em->getRepository(RestaurantTable::class)->createQueryBuilder('rt')->select('count(rt.id)')
        ->where('rt.restaurant = ?1')
        ->andWhere('rt.id <> ?2')
        ->andWhere('rt.number = ?3')
        ->setParameters(array(1 => $restaurant_id, 2 => $id, 3 => $rparams['number']))
        ->getQuery()->getSingleScalarResult();

        if ($check_dup_number > 0) {
            return $this->render('restaurant_tables/edit.html.twig', [
                'table' => $restaurant_table,
                'error' => 'Duplicate restaurant table number'
            ]);
        }

        if ($rparams['number'] < 0) {
            return $this->render('restaurant_tables/edit.html.twig', [
                'table' => $restaurant_table,
                'error' => 'Invalid table number'
            ]);
        }

        if ($rparams['capacity'] < 0) {
            return $this->render('restaurant_tables/edit.html.twig', [
                'table' => $restaurant_table,
                'error' => 'Invalid table capacity'
            ]);
        }

        $restaurant_table->setNumber($rparams['number']);
        $restaurant_table->setCapacity($rparams['capacity']);
        $restaurant_table->setStatus(
            isset($rparams['status']) ? RestaurantTable::STATUS_ACTIVE : RestaurantTable::STATUS_INACTIVE
        );

        $em->persist($restaurant_table);
        $em->flush();

        return $this->render('restaurant_tables/edit.html.twig', [
            'table' => $restaurant_table,
            'info' => 'Successfully updated'
        ]);

        //return new RedirectResponse($this->urlGenerator->generate('edit_restaurant', [
        //    'id' => $id,
        //]));
    }

    /**
     * @Route("/restables/{restaurant_id}", name="create_restaurant_table", methods={"POST"})
     */
    public function createRestaurantTable(Request $request, $restaurant_id)
    {
        $rparams = [
            'number' => $request->request->get('table_number'),
            'capacity' => $request->request->get('table_capacity'),
            'status' => $request->request->get('status'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('create_restaurant_table', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findOneById($restaurant_id);

        $em = $this->getDoctrine()->getManager();

        $check_dup_number = $em->getRepository(RestaurantTable::class)->createQueryBuilder('rt')->select('count(rt.id)')
        ->where('rt.restaurant = ?1')
        ->andWhere('rt.number = ?2')
        ->setParameters(array(1 => $restaurant_id, 2 => $rparams['number']))
        ->getQuery()->getSingleScalarResult();

        if ($check_dup_number > 0) {
            return $this->render('restaurant_tables/create.html.twig', [
                'restaurant' => $restaurant,
                'error' => 'Duplicate restaurant table number'
            ]);
        }

        if ($rparams['number'] < 0) {
            return $this->render('restaurant_tables/create.html.twig', [
                'restaurant' => $restaurant,
                'error' => 'Invalid table number'
            ]);
        }

        if ($rparams['capacity'] < 0) {
            return $this->render('restaurant_tables/create.html.twig', [
                'restaurant' => $restaurant,
                'error' => 'Invalid table capacity'
            ]);
        }

        $restaurant_table = new RestaurantTable;
        $restaurant_table->setNumber($rparams['number']);
        $restaurant_table->setCapacity($rparams['capacity']);
        $restaurant_table->setRestaurant($restaurant);
        $restaurant_table->setStatus(
            isset($rparams['status']) ? RestaurantTable::STATUS_ACTIVE : RestaurantTable::STATUS_INACTIVE
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($restaurant_table);
        $em->flush();

        return $this->render('restaurant_tables/edit.html.twig', [
            'table' => $restaurant_table,
            'info' => 'Successfully created'
        ]);

        //return new RedirectResponse($this->urlGenerator->generate('edit_restaurant', [
        //    'id' => $id,
        //]));
    }
}
