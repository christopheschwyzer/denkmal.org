<?php

class Denkmal_Component_EventDetails extends Denkmal_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $event = $this->_params->getEvent('event');

        $venue = $event->getVenue();

        $mapLink = null;
        if ($venue->getCoordinates() && !$venue->getSecret()) {
            $mapLink = CM_Util::link('https://www.google.com/maps', [
                'q' => $venue->getName() . '@' . $venue->getCoordinates()->getLatitude() . ',' . $venue->getCoordinates()->getLongitude(),
            ]);
        }

        $viewResponse->set('event', $event);
        $viewResponse->set('venue', $venue);
        $viewResponse->set('mapLink', $mapLink);

        $this->_params = CM_Params::factory(); // Empty params to not send them to client
    }
}
