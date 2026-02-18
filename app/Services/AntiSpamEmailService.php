<?php

namespace App\Services;

class AntiSpamEmailService
{
    private string $localPart;

    private string $domain;

    private string $tld;

    public function __construct(protected string $email)
    {
        $parts = explode('@', $this->email, 2);
        $this->localPart = $parts[0];
        $domain = $parts[1] ?? '';
        $domainParts = explode('.', $domain, 2);
        $this->domain = $domainParts[0];
        $this->tld = $domainParts[1] ?? '';
    }

    public function isValid($email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function forMailtoLink(): string
    {
        return $this->isValid($this->email)
            ? $this->emailToAntiSpamMialto($this->email)
            : $this->email;
    }

    public function forDisplay(): string
    {
        return $this->isValid($this->email)
            ? $this->emailToAntiSpamDisplay($this->email)
            : $this->email;
    }

    private function emailToAntiSpamMialto($email): string
    {
        return bin2hex(strrev("{$this->domain}!{$this->tld}!{$this->localPart}"));
    }

    private function emailToAntiSpamDisplay($email)
    {
        $randomClasses = ['txt', 'info', 'data', 'content', 'text'];
        shuffle($randomClasses);

        $antiSpamEmail = '<span class="'.$randomClasses[0].'">'.htmlspecialchars($this->localPart).'</span>';
        $antiSpamEmail .= '<span class="'.$randomClasses[1].'">&#64;</span>';
        $antiSpamEmail .= '<span class="'.$randomClasses[2].'">'.htmlspecialchars($this->domain).'</span>';
        $antiSpamEmail .= '<span class="'.$randomClasses[3].'" aria-hidden="true">'.htmlspecialchars('-info').'</span>';
        $antiSpamEmail .= '<span class="'.$randomClasses[4].'">&#46;'.htmlspecialchars($this->tld).'</span>';

        return $antiSpamEmail;
    }
}
