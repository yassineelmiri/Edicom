<?php

namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\Enum\RoleEnum;
use App\Entity\User;
use App\Handler\UserHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends TestCase
{
    private $userHandler;
    private $jwtEncoder;
    private $userController;

    protected function setUp(): void
    {
        $this->userHandler = $this->createMock(UserHandler::class);
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->userController = new UserController($this->userHandler, $this->jwtEncoder);
    }

    public function testCreate()
    {
        $request = new Request();
        $this->userHandler->method('create')
            ->willReturn(['id' => 1, 'username' => 'john.doe']);

        $response = $this->userController->create($request);
        $this->assertEquals(['id' => 1, 'username' => 'john.doe'], $response);
    }

    public function testUpdate()
    {
        $request = new Request();
        $user = new User();
        $user->setId(1);
        $user->setRole(RoleEnum::ROLE_ADMIN);

        $this->userHandler->method('update')
            ->willReturn(['id' => 1, 'username' => 'john.doe']);

        $response = $this->userController->update($request, 1);
        $this->assertEquals(['id' => 1, 'username' => 'john.doe'], $response);
    }

    public function testDelete()
    {
        $request = new Request();
        $user = new User();
        $user->setId(1);
        $user->setRole(RoleEnum::ROLE_ADMIN);

        $this->userHandler->method('delete')
            ->willReturn(null);

        $response = $this->userController->delete($request, 1);
        $this->assertNull($response);
    }

    public function testGetAll()
    {
        $request = new Request();
        $this->userHandler->method('getAll')
            ->willReturn([['id' => 1, 'username' => 'john.doe']]);

        $response = $this->userController->getAll($request);
        $this->assertEquals([['id' => 1, 'username' => 'john.doe']], $response);
    }


}