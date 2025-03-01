<?php

namespace App\Controller;

use App\Entity\Enum\RoleEnum;
use App\Handler\UserHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;

class UserController extends BaseController
{
    private UserHandler $handler;

    public function __construct(UserHandler $handler, JWTEncoderInterface $JWTEncoder)
    {
        $this->handler = $handler;
        $this->JWTEncoder = $JWTEncoder;
    }

    #[Route('/api/user', methods: ['POST'])]
    #[OA\Post(
        path: "/api/user",
        summary: "Créer un nouvel utilisateur",
        description: "Cette route permet de créer un nouvel utilisateur.",
        requestBody: new OA\RequestBody(
            description: "Informations de l'utilisateur",
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "john.doe"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur créé",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "username", type: "string", example: "john.doe"),
                        new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Données invalides"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function create(Request $request)
    {
        return $this->handler->create($request);
    }

    #[Route('/api/user/{id}', methods: ['PUT'])]
    #[OA\Put(
        path: "/api/user/{id}",
        summary: "Mettre à jour un utilisateur",
        description: "Cette route permet de mettre à jour les informations d'un utilisateur existant.",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            description: "Informations de l'utilisateur à mettre à jour",
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "john.doe"),
                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Utilisateur mis à jour",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "username", type: "string", example: "john.doe"),
                        new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Non autorisé"),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function update(Request $request, int $id)
    {
        $user = $this->getCurrentUser($request);

        if (empty($user)) {
            return $this->unauthorizedResponse();
        }

        return $this->handler->update($request, $id, $user);
    }

    #[Route('/api/user/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/api/user/{id}",
        summary: "Supprimer un utilisateur",
        description: "Cette route permet de supprimer un utilisateur.",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Utilisateur supprimé"),
            new OA\Response(response: 401, description: "Non autorisé"),
            new OA\Response(response: 403, description: "Accès refusé"),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function delete(Request $request, int $id)
    {
        $user = $this->getCurrentUser($request);

        if (empty($user) || ($user->getId() !== $id && $user->getRole() !== RoleEnum::ROLE_ADMIN)) {
            return $this->unauthorizedResponse();
        }

        return $this->handler->delete($id);
    }

    #[Route('/api/users', methods: ['GET'])]
    #[OA\Get(
        path: "/api/users",
        summary: "Récupérer tous les utilisateurs",
        description: "Cette route permet de récupérer la liste de tous les utilisateurs.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des utilisateurs",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "username", type: "string", example: "john.doe"),
                            new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                        ]
                    )
                )
            ),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function getAll(Request $request)
    {
        return $this->handler->getAll($request);
    }

    #[Route('/api/user/me', methods: ['GET'])]
    #[OA\Get(
        path: "/api/user/me",
        summary: "Récupérer les informations de l'utilisateur connecté",
        description: "Cette route permet de récupérer les informations de l'utilisateur actuellement connecté.",
        security: [["Bearer" => []]], 
        responses: [
            new OA\Response(
                response: 200,
                description: "Succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "username", type: "string", example: "john.doe"),
                        new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Non autorisé"),
            new OA\Response(response: 500, description: "Erreur serveur")
        ]
    )]
    public function getMe(Request $request)
    {
        $user = $this->getCurrentUser($request);

        if (empty($user)) {
            return $this->unauthorizedResponse();
        }

        return $this->handler->get($request, $user->getId());
    }
    

   
}