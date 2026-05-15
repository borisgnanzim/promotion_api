<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\OtpNotification;
use App\Repositories\OtpCodeRepositoryInterface;
use Illuminate\Support\Carbon;

class OtpService
{
    public function __construct(private OtpCodeRepositoryInterface $otpCodeRepository)
    {
    }

    public function sendOtp(User $user, ?string $channel = null): void
    {
        $otp = (string) random_int(100000, 999999);
        $channel = $this->resolveChannel($user, $channel);
        $contact = $this->resolveContact($user, $channel);
        $contactType = $this->resolveContactType($channel);

        $this->otpCodeRepository->updateOrCreate(
            $contact,
            $contactType,
            $otp,
            now()->addMinutes(10)
        );

        $user->notify(new OtpNotification($otp, $channel));
    }

    public function verifyOtp(string $contact, string $code): ?User
    {
        $otpCode = $this->otpCodeRepository->findValidCode($contact, $code);

        if (!$otpCode) {
            return null;
        }

        $this->otpCodeRepository->deleteByContact($contact);

        return User::where('email', $contact)
            ->orWhere('phone_number', $contact)
            ->first();
    }

    public function findUserByContact(array $attributes): ?User
    {
        if (!empty($attributes['email'])) {
            return User::where('email', $attributes['email'])->first();
        }

        if (!empty($attributes['phone_number'])) {
            return User::where('phone_number', $attributes['phone_number'])->first();
        }

        return null;
    }

    private function resolveChannel(User $user, ?string $channel): string
    {
        if ($channel === 'sms' && $user->phone_number) {
            return 'sms';
        }

        if ($channel === 'mail' && $user->email) {
            return 'mail';
        }

        if ($user->phone_number) {
            return 'sms';
        }

        return 'mail';
    }

    private function resolveContact(User $user, string $channel): string
    {
        return $channel === 'sms' ? $user->phone_number : $user->email;
    }

    private function resolveContactType(string $channel): string
    {
        return $channel === 'sms' ? 'phone' : 'email';
    }
}
