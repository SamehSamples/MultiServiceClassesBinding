<?php

namespace App\Services\Newsletter\Interfaces;

interface NewsletterInterface
{
    public function subscribe(string $email, string $list = null);
}
