<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Unicorn;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/posts")
 */
class PostController extends BaseController
{

    /**
     * Display listing of the posts.
     *
     * @Route("", methods={"GET"})
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    public function posts(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();

        $data = $this->getSerializer()->serialize($posts, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Create new post and make link to unicorn
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
        if (empty($data['author_name']) || empty($data['message']) ) {
            return $this->json(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        $post = new Post();
        $post->setAuthorName($data['author_name']);
        $post->setMessage($data['message']);

        if(!empty($data['unicorn_id']))
        {
            $unicorn = $entityManager
                ->getRepository(Unicorn::class)
                ->find($data['unicorn_id']);

            if (!$unicorn) {
                return $this->json(['message' => 'Unicorn not found'], Response::HTTP_BAD_REQUEST);
            }

            if ($unicorn->getPurchased()) {
                return $this->json(['message' => 'Unicorn is private property. You can\'t make post on thin unicorn'], Response::HTTP_BAD_REQUEST);
            }

            $post->setUnicorn($unicorn);
        }

        $entityManager->persist($post);
        $entityManager->flush();

        $data = $this->getSerializer()->serialize($post, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
    }

    /**
     * Display all posts by author.
     *
     * @Route("/author/{authorName}", methods={"GET"})
     * @param PostRepository $postRepository
     * @param string $authorName
     * @return JsonResponse
     */
    public function showByAuthor(PostRepository $postRepository, string $authorName): JsonResponse
    {
        $posts = $postRepository->findBy(['authorName' => $authorName]);

        $data = $this->getSerializer()->serialize($posts, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Display a specific post
     *
     * @Route("/{id}", methods={"GET"})
     * @param int $id
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    public function show(int $id, PostRepository $postRepository): JsonResponse
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return $this->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->getSerializer()->serialize($post, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Modify post or link the post to a unicorn
     *
     * @Route("/{id}", methods={"PUT"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $post = $entityManager->getRepository(Post::class)->find($id);
        $data = json_decode($request->getContent(), true);

        // Validate request data
        if (empty($data['author_name']) || empty($data['message'])) {
            return $this->json(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        if(!empty($data['unicorn_id']))
        {
            $unicorn = $entityManager
                ->getRepository(Unicorn::class)
                ->find($data['unicorn_id']);

            if (!$unicorn) {
                return $this->json(['message' => 'Unicorn not found'], Response::HTTP_NOT_FOUND);
            }

            if ($unicorn->getPurchased()) {
                return $this->json(['message' => 'Unicorn is private property. You can\'t make post on thin unicorn'], Response::HTTP_BAD_REQUEST);
            }

            $post->setUnicorn($unicorn);
        }

        $post->setAuthorName($data['author_name']);
        $post->setMessage($data['message']);

        $entityManager->flush();

        $data = $this->getSerializer()->serialize($post, 'json', ['json_encode_options' => JSON_UNESCAPED_UNICODE]);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Delete a specific post
     *
     * @Route("/{id}", methods={"DELETE"})
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return JsonResponse
     */
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            return $this->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
