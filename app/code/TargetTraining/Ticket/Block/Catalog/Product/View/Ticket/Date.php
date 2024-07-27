<?php

namespace TargetTraining\Ticket\Block\Catalog\Product\View\Ticket;

use Magenest\Ticket\Block\Product\Ticket;

class Date extends Ticket
{

    public function getDay()
    {
        $location = $this->getFirstLocation();
        $date = $this->getDate($location->getId())->getFirstItem();

        return date('d', strtotime($date->getDateStart()));
    }

    public function getMonth()
    {
        $location = $this->getFirstLocation();
        $date = $this->getDate($location->getId())->getFirstItem();

        return date('M', strtotime($date->getDateStart()));
    }

    private function getFirstLocation()
    {
        return $this->getLocation()->getFirstItem();
    }
}
