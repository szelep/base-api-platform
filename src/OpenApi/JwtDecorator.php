<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\{
    Operation,
    PathItem,
    RequestBody
};
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

/**
 * Decorate openapi factory to append some custom data.
 *
 * @see https://api-platform.com/docs/core/jwt/#adding-endpoint-to-swaggerui-to-retrieve-a-jwt-token
 */
#[AsDecorator(decorates: 'api_platform.openapi.factory')]
final class JwtDecorator implements OpenApiFactoryInterface
{
    /**
     * Public constructor.
     *
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'login' => [
                    'type' => 'string',
                    'required' => true,
                    'example' => 'admin',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                    'required' => true,
                ],
            ],
        ]);

        $pathItem = new PathItem(
            ref: 'JWT Token',
            post: new Operation(
                operationId: 'postCredentialsItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/authentication_token', $pathItem);

        return $openApi;
    }
}
