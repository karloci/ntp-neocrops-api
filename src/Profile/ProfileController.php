<?php

namespace App\Profile;

use App\Core\Controller\ApiController;
use App\Profile\Dto\ChangePasswordDto;
use App\Profile\Dto\UpdateProfileDto;
use App\Profile\UseCase\ChangePasswordUseCase;
use App\Profile\UseCase\UpdateProfileUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends ApiController
{
    #[Route("/profile", name: "profile_update", methods: ["PATCH"])]
    public function updateProfile(#[MapRequestPayload] UpdateProfileDto $updateProfileDto, UpdateProfileUseCase $updateProfileUseCase): JsonResponse
    {
        $user = $updateProfileUseCase->execute($updateProfileDto);

        return $this->getHttpOkResponse($user, ["user:default"]);
    }

    #[Route("/profile/password", name: "profile_password_change", methods: ["PATCH"])]
    public function changePassword(#[MapRequestPayload] ChangePasswordDto $changePasswordDto, ChangePasswordUseCase $changePasswordUseCase): JsonResponse
    {
        $changePasswordUseCase->execute($changePasswordDto);

        return $this->getHttpOkResponse("The password has been successfully changed");
    }
}