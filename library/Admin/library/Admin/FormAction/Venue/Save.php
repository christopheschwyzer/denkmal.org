<?php

class Admin_FormAction_Venue_Save extends Admin_FormAction_Abstract {

    protected function _checkData(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        parent::_checkData($params, $response, $form);
        /** @var Denkmal_Params $params */
        /** @var Denkmal_Params $formParams */
        $formParams = $form->getParams();
        $venue = $formParams->getVenue('venue');
        $region = $venue->getRegion();

        $name = $params->getString('name');
        if ($name !== $venue->getName()) {
            if ($venue = Denkmal_Model_Venue::findByNameOrAlias($region, $name)) {
                $response->addError($response->getRender()->getTranslation('Name already in use.'), 'name');
            }
        }
    }

    protected function _process(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var Denkmal_Params $params */
        /** @var Denkmal_Params $formParams */
        $formParams = $form->getParams();
        $venue = $formParams->getVenue('venue');

        $name = $params->getString('name');
        $url = $params->has('url') ? $params->getString('url') : null;
        $address = $params->has('address') ? $params->getString('address') : null;
        $email = $params->has('email') ? $params->getString('email') : null;
        $twitterUsername = $params->has('twitterUsername') ? $params->getString('twitterUsername') : null;
        $facebookPageId = $params->has('facebookPage') ? $params->getFacebookPage('facebookPage') : null;
        $coordinates = $params->has('coordinates') ? $params->getGeoPoint('coordinates') : null;
        $ignore = $params->getBoolean('ignore');
        $suspended = $params->getBoolean('suspended');
        $secret = $params->getBoolean('secret');

        $venue->setName($name);
        $venue->setUrl($url);
        $venue->setAddress($address);
        $venue->setEmail($email);
        $venue->setTwitterUsername($twitterUsername);
        $venue->setFacebookPage($facebookPageId);
        $venue->setCoordinates($coordinates);
        $venue->setIgnore($ignore);
        $venue->setSuspended($suspended);
        $venue->setSecret($secret);
        $venue->setQueued(false);

        $response->reloadComponent();
    }
}
