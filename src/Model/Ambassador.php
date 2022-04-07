<?php

namespace ShopfrontSDK\Model;

use AnnSummersCommon\Model\Enrollee;

class Ambassador
{
    public function __construct(
        public string $enrollee_id,
        public string $domain,
        public string $email,
        public string $first_name,
        public string $last_name
    )
    {
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
