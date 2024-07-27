<?php

namespace TargetTraining\Ticket\Block\Catalog\Product\View\Ticket;

use Magenest\Ticket\Block\Product\Ticket;

class Location extends Ticket
{
    public function getAvailableLocationsText()
    {
        return implode(', ', $this->getAvailableLocationNames());
    }

    private function getAvailableLocations()
    {
        return $this->getLocation();
    }

    private function getAvailableLocationNames()
    {
        $result = [];
        $locations = $this->getAvailableLocations();

        if (count($locations) === 0) {
            $result[] = __('TBC');
        } else {
            foreach ($locations as $location) {
                $result[] = $location->getLocationTitle();
            }
        }

        return $result;
    }
}
