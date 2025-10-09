<?php

declare(strict_types=1);

namespace App\UI\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/health', name: 'api_health_')]
#[OA\Tag(name: 'Health')]
final class HealthController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    #[Route('', name: 'check', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns the health status of the application',
        summary: 'Health check',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Application is healthy',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'healthy'),
                        new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'services', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 503,
                description: 'Application is unhealthy',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'unhealthy'),
                        new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            )
        ]
    )]
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $errors = [];
        $services = [];

        // Check database connection
        try {
            $this->connection->executeQuery('SELECT 1');
            $services['database'] = 'healthy';
        } catch (\Exception $e) {
            $services['database'] = 'unhealthy';
            $errors[] = 'Database connection failed: ' . $e->getMessage();
            $status = 'unhealthy';
        }
        // Check memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $services['memory'] = [
            'usage' => $this->formatBytes($memoryUsage),
            'limit' => $memoryLimit,
            'status' => $memoryUsage < $this->parseMemoryLimit($memoryLimit) * 0.9 ? 'healthy' : 'warning'
        ];

        // Check disk space
        $freeBytes = disk_free_space('.');
        $totalBytes = disk_total_space('.');
        $services['disk'] = [
            'free' => $this->formatBytes((int)$freeBytes),
            'total' => $this->formatBytes((int)$totalBytes),
            'usage_percent' => round((($totalBytes - $freeBytes) / $totalBytes) * 100, 2),
            'status' => ($freeBytes / $totalBytes) > 0.1 ? 'healthy' : 'warning'
        ];

        $response = [
            'status' => $status,
            'timestamp' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'services' => $services,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return new JsonResponse($response, $status === 'healthy' ? 200 : 503);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $limit = (int) $limit;

        switch ($last) {
            case 'g':
                $limit *= 1024;
            case 'm':
                $limit *= 1024;
            case 'k':
                $limit *= 1024;
        }

        return $limit;
    }
}
