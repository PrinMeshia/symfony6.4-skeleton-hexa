<?php

declare(strict_types=1);

namespace App\Tests\Integration\UI\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class UserControllerTest extends WebTestCase
{
    public function testCreateUserSuccessfully(): void
    {
        $client = static::createClient();

        $jsonData = json_encode([
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);
        
        $this->assertNotFalse($jsonData, 'JSON encoding should not fail');

        $client->request('POST', '/api/users', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $jsonData);

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        
        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals('test@example.com', $responseData['data']['email']);
        $this->assertEquals('John', $responseData['data']['firstName']);
        $this->assertEquals('Doe', $responseData['data']['lastName']);
    }

    public function testCreateUserWithInvalidData(): void
    {
        $client = static::createClient();

        $jsonData = json_encode([
            'email' => 'invalid-email',
            'firstName' => '',
            'lastName' => 'Doe',
        ]);
        
        $this->assertNotFalse($jsonData, 'JSON encoding should not fail');

        $client->request('POST', '/api/users', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $jsonData);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        
        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('violations', $responseData);
    }

    public function testGetUserNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/users/non-existent-id');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        
        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('error', $responseData);
    }
}
