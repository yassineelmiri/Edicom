<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BaseControllerTest extends TestCase
{
    private $jwtEncoder;
    private $baseController;

    protected function setUp(): void
    {
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->baseController = new BaseController($this->jwtEncoder);
    }

    public function testUnauthorizedResponse()
    {
        $response = $this->baseController->unauthorizedResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('{"message":"Unauthorized request"}', $response->getContent());
    }

    public function testForbiddenResponse()
    {
        $response = $this->baseController->forbiddendResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('{"message":"Forbidden request"}', $response->getContent());
    }

    public function testResponseWithMessage()
    {
        $response = $this->baseController->responseWithMessage('Test message', 200);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"message":"Test message"}', $response->getContent());
    }

    public function testGetTokenFromRequest()
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer valid_token');

        $token = $this->baseController->getTokenFromRequest($request);
        $this->assertEquals('valid_token', $token);
    }

    public function testDecodeToken()
    {
        $this->jwtEncoder->method('decode')
            ->willReturn(['id' => 1, 'username' => 'john.doe']);

        $decodedToken = $this->baseController->decodeToken('valid_token');
        $this->assertEquals(['id' => 1, 'username' => 'john.doe'], $decodedToken);
    }

    public function testGetCurrentUser()
    {
        $this->jwtEncoder->method('decode')
            ->willReturn([
                'id' => 1,
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john.doe@example.com',
                'role' => 1,
                'userType' => 1,
                'username' => 'john.doe'
            ]);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer valid_token');

        $user = $this->baseController->getCurrentUser($request);
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('john.doe', $user->getUsername());
    }
}