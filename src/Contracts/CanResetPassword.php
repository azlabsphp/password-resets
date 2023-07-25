<?php

namespace App\Contracts;

interface CanResetPassword
{
    /**
     * return the email, phone number or address for password reset link is sent
     * 
     * @return null|string 
     */
    public function getSubForPasswordResetLink(): ?string;

    /**
     * Return email, phone number or generic addres for otp password reset
     * 
     * @return null|string 
     */
    public function getSubForPasswordResetOtp(): ?string;
}