<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Restaurant;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class RestaurantsController extends AbstractController
{
    private $params;
    private $rootDir;
    private $csrfTokenManager;
    private $urlGenerator;

    public function __construct(
        KernelInterface $kernel,
        ParameterBagInterface $params,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->rootDir = $kernel->getProjectDir();
        $this->params = $params;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/restaurants", name="restaurants", methods={"GET"})
     */
    public function index()
    {
        $restaurants = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();

        return $this->render('restaurants/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/restaurants/search", name="search_restaurants", methods={"POST"})
     */
    public function search(Request $request)
    {
        $rparams = [
            'title' => $request->request->get('title'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('search_restaurants', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $restaurants = $this->getDoctrine()->getManager()->getRepository(Restaurant::class)->createQueryBuilder('r')
           ->where('r.title LIKE :title')
           ->setParameter('title', '%'.$rparams['title'].'%')
           ->getQuery()
           ->getResult();

        return $this->render('restaurants/index.html.twig', [
            'restaurants' => $restaurants,
            'search_title' => $rparams['title']
        ]);
    }


    /**
     * @Route("/restaurants/new", name="new_restaurant", methods={"GET"})
     */
    public function getNewRestaurantView()
    {
        return $this->render('restaurants/create.html.twig');
    }

    /**
     * @Route("/restaurants/{id}", name="edit_restaurant", methods={"GET"})
     */
    public function getRestaurantView($id)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findOneById($id);

        return $this->render('restaurants/edit.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/restaurants/remove/{id}", name="remove_restaurant")
     */
    public function removeRestaurant(Request $request, $id)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findOneById($id);

        if (!isset($restaurant)) {
            return $this->render('restaurants/edit.html.twig', [
                'error' => 'Invalid Record'
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($restaurant);
        $em->flush();

        return new RedirectResponse($this->urlGenerator->generate('restaurants'));
    }

    /**
     * @Route("/restaurants/update/{id}", name="update_restaurant", methods={"POST"})
     */
    public function updateRestaurant(Request $request, $id)
    {
        $rparams = [
            'title' => $request->request->get('title'),
            'status' => $request->request->get('status'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('update_restaurant', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $em = $this->getDoctrine()->getManager();

        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findOneById($id);

        if (!isset($restaurant)) {
            return $this->render('restaurants/edit.html.twig', [
                'error' => 'Invalid Record'
            ]);
        }

        $check_dup_title = $em->getRepository(Restaurant::class)->createQueryBuilder('r')->select('count(r.id)')
        ->where('r.id <> ?1')
        ->andWhere('r.title = ?2')
        ->setParameters(array(1 => $id, 2 => $rparams['title']))
        ->getQuery()->getSingleScalarResult();

        if ($check_dup_title > 0) {
            return $this->render('restaurants/edit.html.twig', [
                'restaurant' => $restaurant,
                'error' => 'Duplicate restaurant title'
            ]);
        }

        if (strlen($rparams['title']) <= 0) {
            return $this->render('restaurants/edit.html.twig', [
                'restaurant' => $restaurant,
                'error' => 'Invalid table title'
            ]);
        }

        $restaurant->setTitle($rparams['title']);
        $restaurant->setStatus(isset($rparams['status']) ? Restaurant::STATUS_ACTIVE : Restaurant::STATUS_INACTIVE);

        $em->persist($restaurant);
        $em->flush();

        return $this->render('restaurants/edit.html.twig', [
            'restaurant' => $restaurant,
            'info' => 'Successfully updated'
        ]);

        //return new RedirectResponse($this->urlGenerator->generate('edit_restaurant', [
        //    'id' => $id,
        //]));
    }

    /**
     * @Route("/restaurants", name="create_restaurant", methods={"POST"})
     */
    public function createRestaurant(Request $request)
    {
        $rparams = [
            'title' => $request->request->get('title'),
            'status' => $request->request->get('status'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('create_restaurant', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $em = $this->getDoctrine()->getManager();

        $check_dup_title = $em->getRepository(Restaurant::class)->createQueryBuilder('r')->select('count(r.id)')
        ->where('r.title = ?1')
        ->setParameters(array(1 => $rparams['title']))
        ->getQuery()->getSingleScalarResult();

        if ($check_dup_title > 0) {
            return $this->render('restaurants/create.html.twig', [
                'error' => 'Duplicate restaurant title'
            ]);
        }

        if (strlen($rparams['title']) <= 0) {
            return $this->render('restaurants/create.html.twig', [
                'error' => 'Invalid table title'
            ]);
        }

        $restaurant = new Restaurant;
        $restaurant->setTitle($rparams['title']);
        $restaurant->setStatus(isset($rparams['status']) ? Restaurant::STATUS_ACTIVE : Restaurant::STATUS_INACTIVE);

        $em->persist($restaurant);
        $em->flush();

        return $this->render('restaurants/edit.html.twig', [
            'restaurant' => $restaurant,
            'info' => 'Successfully created'
        ]);

        //return new RedirectResponse($this->urlGenerator->generate('edit_restaurant', [
        //    'id' => $id,
        //]));
    }

    /**
     * @Route("/restaurants/{id}/image", name="upload_restaurant_image", methods={"POST"})
     */
    public function uploadRestaurantImage(Request $request, $id)
    {
        $imagesFolderPath = $this->rootDir.'/'.$this->params->get('app.repository_path');

        $rparams = [
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('upload_restaurant_photo', $rparams['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII;
            //[^A-Za-z0-9_] remove; Lower()', $originalFilename);
            //$newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            $newFilename = $originalFilename.'.'.$uploadedFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $uploadedFile->move(
                    $imagesFolderPath,
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $restaurant = $this->getDoctrine()
                ->getRepository(Restaurant::class)
                ->findOneById($id);

            if (!isset($restaurant)) {
                return $this->render('restaurants/edit.html.twig', [
                    'error' => 'Invalid Record'
                ]);
            }

            $restaurant->setPhoto($newFilename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurant);
            $em->flush();
        }

        return new Response(
            $newFilename,
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/restaurants/images/{filename}", name="restaurants_images", methods={"GET"})
     */
    public function getImage($filename)
    {
        $imagesFolderPath = $this->rootDir.'/'.$this->params->get('app.repository_path');

        // This should return the file located in /mySymfonyProject/web/public-resources/TextFile.txt
        // to being viewed in the Browser
        return new BinaryFileResponse($imagesFolderPath.'/'.$filename);
    }
}
