<?php

namespace ShopfrontSDK\Model;

use PlumTreeCommon\Model\Event as CommonEvent;

class Event
{
    public function __construct(
        public string $eventCode,
        public string $title,
        public string $eventDateFrom,
        public string $eventDateTo,
        public string $campaignDateFrom,
        public string $campaignDateTo,
        public bool $isCancelled
    ) {
    }

    public static function fromCommon(CommonEvent $event): self
    {
        return new self(
            $event->eventCode,
            $event->title,
            $event->eventDateFrom->format(\DateTimeInterface::ATOM),
            $event->eventDateTo->format(\DateTimeInterface::ATOM),
            $event->campaignDateFrom->format(\DateTimeInterface::ATOM),
            $event->campaignDateTo->format(\DateTimeInterface::ATOM),
            $event->canceled,
        );
    }
}
