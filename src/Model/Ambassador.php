<?php

namespace ShopfrontSDK\Model;

use PlumTreeCommon\Model\Enrollee;

class Ambassador
{
    public string $enrolleeId;
    public string $domain;
    public string $email;
    public string $firstName;
    public string $lastName;
    public bool $disable;

    public function __construct(
        string $enrolleeId,
        string $domain,
        string $email,
        string $firstName,
        string $lastName,
        bool $disable
    ) {
        $this->enrolleeId = $enrolleeId;
        $this->domain = $domain;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->disable = $disable;
    }

    public static function fromEnrollee(Enrollee $enrollee): self
    {
        return new self(
            $enrollee->enrolleeId ?? '',
            $enrollee->meta->micrositeUrl ?? '',
            $enrollee->profile->email ?? '',
            $enrollee->profile->firstName ?? '',
            $enrollee->profile->lastName ?? '',
            $enrollee->disabled
        );
    }
}
