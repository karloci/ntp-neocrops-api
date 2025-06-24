<?php

namespace App\User\Controller;

use App\Core\Controller\ApiController;
use App\Entity\Farm;
use App\User\Dto\UserDto;
use App\User\UseCase\CreateUserUseCase;
use App\User\UseCase\DeleteUserUseCase;
use App\User\UseCase\FindAllUsersUseCase;
use App\User\UseCase\UpdateUserUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends ApiController
{
    #[Route("/farm/{farm}/users", name: "users_list", methods: ["GET"])]
    public function getAllUsers(Farm $farm, FindAllUsersUseCase $findAllUsersUseCase): JsonResponse
    {
        $users = $findAllUsersUseCase->execute($farm);

        return $this->getHttpOkResponse($users, ["user:default"]);
    }

    #[Route("/farm/{farm}/users", name: "users_create", methods: ["POST"])]
    public function createUser(Farm $farm, #[MapRequestPayload] UserDto $userDto, CreateUserUseCase $createUserUseCase): JsonResponse
    {
        $user = $createUserUseCase->execute($userDto, $farm);

        return $this->getHttpCreatedResponse($user, ["user:default"]);
    }

    #[Route("/farm/{farm}/users/{userId}", name: "users_update", methods: ["PATCH"])]
    public function updateUser(string $userId, #[MapRequestPayload] UserDto $userDto, UpdateUserUseCase $updateUserUseCase): JsonResponse
    {
        $user = $updateUserUseCase->execute($userId, $userDto);

        return $this->getHttpOkResponse($user, ["user:default"]);
    }

    #[Route("/farm/{farm}/users/{userId}", name: "users_delete", methods: ["DELETE"])]
    public function deleteUser(string $userId, DeleteUserUseCase $deleteUserUseCase): JsonResponse
    {
        $deleteUserUseCase->execute($userId);

        return $this->getHttpNoContentResponse();
    }
}