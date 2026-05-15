<?php

namespace App\Repositories;

use App\Models\OtpCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class OtpCodeRepository implements OtpCodeRepositoryInterface
{
    public function updateOrCreate(string $contact, string $contactType, string $code, Carbon $expiresAt): OtpCode
    {
        return OtpCode::updateOrCreate(
            ['contact' => $contact],
            ['contact_type' => $contactType, 'code' => $code, 'expires_at' => $expiresAt]
        );
    }

    public function findValidCode(string $contact, string $code): ?OtpCode
    {
        return OtpCode::where('contact', $contact)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteByContact(string $contact): int
    {
        return OtpCode::where('contact', $contact)->delete();
    }
}
