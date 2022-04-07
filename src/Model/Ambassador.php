<?php

namespace ShopfrontSDK\Model;

use AnnSummersCommon\Model\Enrollee;

class Ambassador
{
    public string $enrolleeId;
    public string $domain;
    public string $email;
    public string $firstName;
    public string $lastName;

    public function __construct(
        string $enrolleeId,
        string $domain,
        string $email,
        string $firstName,
        string $lastName
    ) {
        $this->enrolleeId = $enrolleeId;
        $this->domain = $domain;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public static function fromEnrollee(Enrollee $enrollee): self
    {
        return new self(
            $enrollee->enrolleeId ?? '',
            $enrollee->meta->micrositeUrl ?? '',
            $enrollee->profile->email ?? '',
            $enrollee->profile->firstName ?? '',
            $enrollee->profile->lastName ?? ''
        );
    }
}
