<?php

namespace ShopfrontSDK\Model;

// use AnnSummersCommon\Model\Enrollee;

class Ambassador
{
    public function __construct(
        public string $id,
        public string $parentId,
        public string $domain,
        public string $email,
        public string $firstName,
        public string $lastName
    )
    {
    }

    // public static function fromEnrollee(Enrollee $enrollee): self
    // {
    //     return new self(
    //         $enrollee->enrolleeId ?? '',
    //         $enrollee->parentId ?? '',
    //         $enrollee->meta->micrositeUrl ?? '',
    //         $enrollee->profile->email ?? '',
    //         $enrollee->profile->firstName ?? '',
    //         $enrollee->profile->lastName ?? ''
    //     );
    // }
}
