<?php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use App\Handler\SecurityHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends TestCase
{
    private $securityHandler;
    private $jwtEncoder;
    private $securityController;

    protected function setUp(): void
    {
        $this->securityHandler = $this->createMock(SecurityHandler::class);
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->securityController = new SecurityController($this->securityHandler, $this->jwtEncoder);
    }

    public function testLogin()
    {
        $request = new Request();
        $this->securityHandler->method('login')
            ->willReturn(['token' => 'fake_token']);

        $response = $this->securityController->login($request);
        $this->assertEquals(['token' => 'fake_token'], $response);
    }
}