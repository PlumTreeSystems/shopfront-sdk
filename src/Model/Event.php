<?php

namespace ShopfrontSDK\Model;

use PlumTreeCommon\Model\Event as CommonEvent;

class Event
{
    public function __construct(
        public string $eventCode,
        public string $title,
        public \DateTime $eventDateFrom,
        public \DateTime $eventDateTo,
        public \DateTime $campaignDateFrom,
        public \DateTime $campaignDateTo,
        public bool $isCancelled
    ) {
    }

    public static function fromCommon(CommonEvent $event): self
    {
        return new self(
            $event->eventCode,
            $event->title,
            $event->eventDateFrom,
            $event->eventDateTo,
            $event->campaignDateFrom,
            $event->campaignDateTo,
            $event->canceled,
        );
    }
}
