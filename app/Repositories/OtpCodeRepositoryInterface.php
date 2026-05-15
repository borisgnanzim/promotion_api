<?php

namespace App\Repositories;

use App\Models\OtpCode;
use Illuminate\Support\Carbon;

interface OtpCodeRepositoryInterface
{
    public function updateOrCreate(string $contact, string $contactType, string $code, Carbon $expiresAt): OtpCode;

    public function findValidCode(string $contact, string $code): ?OtpCode;

    public function deleteByContact(string $contact): int;
}
