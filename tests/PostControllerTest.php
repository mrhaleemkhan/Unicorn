<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testGetAllPosts()
    {
        $client = static::createClient();
        $client->request('GET', '/posts');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreatePost()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/posts',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"author_name": "Haleem", "message": "This is a new post."}'
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetPost()
    {
        $client = static::createClient();
        $client->request('GET', '/posts/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetAuthorPost()
    {
        $client = static::createClient();
        $client->request('GET', '/posts/author/Haleem');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, count(json_decode($client->getResponse()->getContent(), true)));
    }

    public function testUpdatePost()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/posts/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"author_name": "Haleem", "message": "This is a updated post."}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeletePost()
    {
        $client = static::createClient();
        $client->request('DELETE', '/posts/1');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}
