<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\Unicorn;
use App\Message\EmailMessage;
use App\Repository\UnicornRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UnicornController
 * @package App\Controller
 * @Route("/unicorns")
 */
class UnicornController extends BaseController
{
    /**
     * Display listing of the unicorn resource.
     *
     * @Route("", methods={"GET"})
     * @param UnicornRepository $unicornRepository
     * @return JsonResponse
     */
    public function index(UnicornRepository $unicornRepository): JsonResponse
    {
        $unicorns = $unicornRepository->findAll();
        $data = $this->getSerializer()->serialize($unicorns, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Store a newly created unicorn in database.
     *
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate request data
        if (empty($data['name']) || empty($data['color']) || empty($data['age']) || empty($data['price']) ) {
            return $this->json(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        $unicorn = new Unicorn();
        $unicorn->setName($data['name']);
        $unicorn->setColor($data['color']);
        $unicorn->setAge($data['age']);
        $unicorn->setPrice($data['price']);

        $entityManager->persist($unicorn);
        $entityManager->flush();

        $data = $this->getSerializer()->serialize($unicorn, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
    }

    /**
     * Display the specified unicorn
     *
     * @Route("/{id}", methods={"GET"})
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $unicornRepository = $entityManager->getRepository(Unicorn::class);
        $unicorn = $unicornRepository->find($id);

        if (!$unicorn) {
            return $this->json(['error' => 'Unicorn not found'], 404);
        }

        $data = $this->getSerializer()->serialize($unicorn, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Display the all posts related to unicorn
     *
     * @Route("/{id}/posts", methods={"GET"})
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function getPosts(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $unicornRepository = $entityManager->getRepository(Unicorn::class);
        $unicorn = $unicornRepository->find($id);

        if (!$unicorn) {
            return $this->json(['error' => 'Unicorn not found'], 404);
        }

        $posts = $unicorn->getPosts();

        $data = $this->getSerializer()->serialize($posts, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Purchase unicorn and delete all posts related to unicorn
     *
     * @Route("/{id}/purchase", name="unicorn_purchase", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $messageBus
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function purchase(Request $request, EntityManagerInterface $entityManager, MessageBusInterface $messageBus, int $id): JsonResponse
    {

        $unicorn = $entityManager->getRepository(Unicorn::class)->find($id);

        if (!$unicorn) {
            return $this->json(['message' => 'Unicorn not found'], Response::HTTP_NOT_FOUND);
        }

        if ($unicorn->getPurchased()) {
            return $this->json(['message' => 'Unicorn is already purchased.'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        // Validate request data
        if (empty($data['buyer_name']) || empty($data['buyer_email']) ) {
            return $this->json(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        // Create a new Purchase entity
        $purchase = new Purchase();
        $purchase->setBuyerName($data['buyer_name']);
        $purchase->setBuyerEmail($data['buyer_email']);
        $purchase->setPurchaseDate(new \DateTime());
        $purchase->setUnicorn($unicorn);

        $posts = $unicorn->getPosts();
        $counter = count($posts);

        // Delete all posts linked to the purchased unicorn
        foreach ($posts as $post) {
            $entityManager->remove($post);
        }

        // Persist the new purchase and flush changes to the database
        $entityManager->persist($purchase);
        $entityManager->flush();

        // Build Congratulations email for buyer
        $emailMessage = new EmailMessage($data['buyer_email'], $counter);

        // Dispatch the email message to the message bus for processing
        $messageBus->dispatch($emailMessage);

        return new JsonResponse(['message' => 'Unicorn purchased successfully. All posts linked to the unicorn have been deleted.'], Response::HTTP_CREATED);
    }

}
