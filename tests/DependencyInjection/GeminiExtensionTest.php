<?php

declare(strict_types=1);

namespace Gemini\Symfony\Tests\DependencyInjection;

use Gemini\Client;
use Gemini\Symfony\DependencyInjection\GeminiExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GeminiExtensionTest extends TestCase
{
    public function test_service(): void
    {
        // Using a mock to test the service configuration
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options = []) {
            self::assertSame('GET', $method);
            self::assertSame('https://generativelanguage.googleapis.com/v1beta/models', $url);
            self::assertContains('x-goog-api-key: 123456789', $options['headers']);

            return new MockResponse('{ "models": [ { "name": "models/chat-bison-001", "version": "001", "displayName": "Chat Bison", "description": "Chat-optimized generative language model.", "inputTokenLimit": 4096, "outputTokenLimit": 1024, "supportedGenerationMethods": [ "generateMessage", "countMessageTokens" ], "temperature": 0.25, "topP": 0.95, "topK": 40 }] }', [
                'http_code' => 200,
                'response_headers' => [
                    'content-type' => 'application/json',
                ],
            ]);
        });

        $container = new ContainerBuilder;
        $container->set('http_client', $httpClient);

        $extension = new GeminiExtension;
        $extension->load([
            'gemini' => [
                'api_key' => '123456789',
            ],
        ], $container);

        $gemini = $container->get('gemini');
        self::assertInstanceOf(Client::class, $gemini);

        $response = $gemini->models()->list();
        self::assertCount(1, $response->models);
        self::assertSame([
            'models' => [
                [
                    'name' => 'models/chat-bison-001',
                    'version' => '001',
                    'displayName' => 'Chat Bison',
                    'description' => 'Chat-optimized generative language model.',
                    'inputTokenLimit' => 4096,
                    'outputTokenLimit' => 1024,
                    'supportedGenerationMethods' => [
                        'generateMessage',
                        'countMessageTokens',
                    ],
                    'baseModelId' => null,
                    'temperature' => 0.25,
                    'maxTemperature' => null,
                    'topP' => 0.95,
                    'topK' => 40,
                ],
            ],
            'nextPageToken' => null,
        ], $response->toArray());
    }
}
