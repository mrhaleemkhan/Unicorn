<?php

namespace App\Tests\Controller;

use App\Entity\Unicorn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UnicornControllerTest extends WebTestCase
{
    public function testGetUnicorns()
    {
        $client = static::createClient();

        $client->request('GET', '/unicorns');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateAUnicorn()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/unicorns',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name": "Test Unicorn", "description": "A test unicorn", "color": "White", "age": "20", "price": "100.00"}'
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testCreateBUnicorn()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/unicorns',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name": "Test Unicorn", "description": "B test unicorn", "color": "White", "age": "20", "price": "100.00"}'
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetUnicorn()
    {
        $client = static::createClient();

        $client->request('GET', '/unicorns/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


    // Make post on Unicorn
    public function testCreatePostOnUnicorn()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/posts',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"author_name": "Haleem", "message": "This is a new post.", "unicorn_id" : 1}'
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * @depends("testCreateAUnicorn")
     */
    public function testPurchase(): void
    {
        // Make a purchase request with valid data

        $client = static::createClient();
        $client->request(
            'POST',
            '/unicorns/1/purchase',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['buyer_name' => 'Test Buyer', 'buyer_email' => 'testbuyer@example.com'])
        );

        // Check that the response has the correct status code and message
        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Unicorn purchased successfully. All posts linked to the unicorn have been deleted.', json_decode($response->getContent(), true)['message']);
    }
}
