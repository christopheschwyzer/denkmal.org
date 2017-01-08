<?php

class Admin_FormAction_UserEdit_Save extends Admin_FormAction_Abstract {

    protected function _process(CM_Params $params, CM_Http_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var Denkmal_Params $params */
        /** @var Denkmal_Params $paramsForm */
        $paramsForm = $form->getParams();

        $user = $paramsForm->getUser('user');
        $email = $params->getString('email');
        $username = $params->getString('username');
        $password = $params->has('password') ? $params->getString('password') : null;

        $user->setEmail($email);
        $user->setUsername($username);
        if (null !== $password) {
            $user->setPassword($password);
        }

        $response->reloadComponent();
    }
}
