<?php

class Admin_FormAction_VenueMerge_Merge extends Admin_FormAction_Abstract {

    protected function _checkData(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        parent::_checkData($params, $response, $form);
        /** @var Denkmal_Params $params */
        $oldVenue = $params->getVenue('oldVenue');
        $newVenue = $params->getVenue('newVenue');
        if ($newVenue->equals($oldVenue)) {
            $response->addError($response->getRender()->getTranslation('Venue can\'t be replaced by itself.'), 'newVenue');
        }
    }

    protected function _process(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var Denkmal_Params $params */
        $oldVenue = $params->getVenue('oldVenue');
        $newVenue = $params->getVenue('newVenue');

        /** @var Denkmal_Model_Event $event */
        foreach ($oldVenue->getEventList() as $event) {
            $event->setVenue($newVenue);
        }

        Denkmal_Model_VenueAlias::create($newVenue, $oldVenue->getName());
        /** @var Denkmal_Model_VenueAlias $alias */
        foreach ($oldVenue->getAliasList() as $alias) {
            $alias->setVenue($newVenue);
        }

        $oldVenue->delete();
    }
}
