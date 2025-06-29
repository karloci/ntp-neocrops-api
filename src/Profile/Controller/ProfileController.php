<?php

namespace App\Profile\Controller;

use App\Core\Controller\ApiController;
use App\Core\Service\ContextService;
use App\Profile\Dto\ChangePasswordDto;
use App\Profile\Dto\UpdateProfileDto;
use App\Profile\UseCase\ChangePasswordUseCase;
use App\Profile\UseCase\DeleteAvatarUserCase;
use App\Profile\UseCase\GetAvatarUserCase;
use App\Profile\UseCase\UpdateProfileUseCase;
use App\Profile\UseCase\UploadAvatarUserCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route("/profile/avatar", name: "profile_get_avatar", methods: ["GET"])]
    public function getAvatar(GetAvatarUserCase $getAvatarUserCase): Response
    {
        $avatar = $getAvatarUserCase->execute();

        if (!$avatar) {
            return $this->getHttpNoContentResponse();
        }

        $content = stream_get_contents($avatar);
        return new Response($content, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-store',
        ]);

    }

    #[Route("/profile/avatar", name: "profile_upload_avatar", methods: ["POST"])]
    public function uploadAvatar(Request $request, UploadAvatarUserCase $uploadAvatarUserCase, ContextService $contextService): Response
    {
        $uploadAvatarUserCase->execute($request);

        return $this->getHttpOkResponse($contextService->translate("The avatar has been successfully uploaded"));
    }

    #[Route("/profile/avatar", name: "profile_delete_avatar", methods: ["DELETE"])]
    public function deleteAvatar(DeleteAvatarUserCase $deleteAvatarUserCase, ContextService $contextService): Response
    {
        $deleteAvatarUserCase->execute();

        return $this->getHttpOkResponse($contextService->translate("The avatar has been successfully deleted"));
    }
}