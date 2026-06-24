<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UploadAvatarRequest;

use App\Services\ProfileService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProfileService $profileService
    ) {
    }

    /**
     * Show profile.
     */
    public function show(
        Request $request
    ) {
        return $this->successResponse(
            $this->profileService->show(
                $request->user()
            ),
            'Profil berhasil diambil'
        );
    }

    /**
     * Update profile.
     */
    public function update(
        UpdateProfileRequest $request
    ) {
        $profile = $this->profileService->update(
            user: $request->user(),
            data: $request->validated()
        );

        return $this->successResponse(
            $profile,
            'Profil berhasil diperbarui'
        );
    }

    /**
     * Update password.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ) {
        $this->profileService->updatePassword(
            user: $request->user(),
            data: $request->validated()
        );

        return $this->successResponse(
            null,
            'Password berhasil diperbarui'
        );
    }

    /**
     * Upload avatar.
     */
    public function uploadAvatar(
        UploadAvatarRequest $request
    ) {
        $profile = $this->profileService->uploadAvatar(
            user: $request->user(),
            avatar: $request->file('avatar')
        );

        return $this->successResponse(
            $profile,
            'Avatar berhasil diupload'
        );
    }

    /**
     * Delete avatar.
     */
    public function deleteAvatar(
        Request $request
    ) {
        $profile = $this->profileService->deleteAvatar(
            $request->user()
        );

        return $this->successResponse(
            $profile,
            'Avatar berhasil dihapus'
        );
}
    }