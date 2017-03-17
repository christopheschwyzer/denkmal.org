<?php

class Denkmal_FormAction_EventAdd_Create extends CM_FormAction_Abstract {

    protected function _checkData(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var Denkmal_Params $params */
        $event = Denkmal_Form_EventAdd::getEventFromData($params);
        $now = new DateTime();

        if ($event->getFrom() < $now && !$event->getUntil()) {
            $response->addError($response->getRender()->getTranslation('Event is in the past.'), 'date');
        }
        if ($event->getUntil() < $now && $event->getUntil()) {
            $response->addError($response->getRender()->getTranslation('Event is in the past.'), 'date');
        }

        if (!$params->has('title') && !$params->has('artists')) {
            $response->addError($response->getRender()->getTranslation('Please provide title or artist.'), 'title');
        }
    }

    protected function _process(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var Denkmal_Params $formParams */
        $formParams = $form->getParams();
        $region = $formParams->getRegion('region');

        /** @var Denkmal_Params $params */
        $venue = Denkmal_Form_EventAdd::getVenueFromData($params, $region);
        $venue->commit();

        $event = Denkmal_Form_EventAdd::getEventFromData($params);
        $event->setVenue($venue);
        $event->commit();

        if ($params->has('link')) {
            $linkUrl = $params->getString('link');
            Denkmal_Model_EventLink::create($event, 'Event Link', $linkUrl);
        }

        if ((new DateTime())->add(new DateInterval('PT48H')) > $event->getFrom()) {
            $notificationEmail = new Admin_Mail_EventNotification($event);
            $notificationEmail->send();
        }
    }
}
