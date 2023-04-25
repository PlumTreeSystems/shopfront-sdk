<?php

namespace ShopfrontSDK\Model;

use PlumTreeCommon\Model\Enrollee;
use ShopfrontSDK\Helper\CountryMapHelper;

class Customer
{
    public function __construct(
        public string $enrolleeId,
        public string $domain,
        public string $email,
        public string $firstName,
        public string $lastName,
        public bool $disable,
        public array $address
    ) {
    }

    public static function fromEnrollee(Enrollee $enrollee): self
    {
        return new self(
            $enrollee->enrolleeId ?? '',
            $enrollee->meta->micrositeUrl ?? '',
            $enrollee->profile->email ?? '',
            $enrollee->profile->firstName ?? '',
            $enrollee->profile->lastName ?? '',
            $enrollee->disabled,
            [
                'city' => $enrollee->profile->town ?? '-',
                'country_id' => CountryMapHelper::getMappedCountries()[strtoupper($enrollee->profile->country)],
                'postcode' => $enrollee->profile->postcode,
                'street' => [$enrollee->profile->address],
                'telephone' => $enrollee->profile->phoneNumber
            ]
        );
    }
}
