<?php

class Denkmal_FormField_Venue extends CM_FormField_SuggestOne {

    protected function _initialize() {
        /** @var Denkmal_Params $params */
        $params = $this->getParams();
        $this->_options['region'] = $params->has('region') ? $params->getRegion('region') : null;
        parent::_initialize();
    }

    /**
     * @param CM_Frontend_Environment $environment
     * @param array                   $userInput
     * @return Denkmal_Model_Venue|string
     * @throws CM_Exception_FormFieldValidation
     */
    public function validate(CM_Frontend_Environment $environment, $userInput) {
        $value = parent::validate($environment, $userInput);
        if (null === $value) {
            throw new CM_Exception_FormFieldValidation(new CM_I18n_Phrase('Invalid venue data.'));
        }
        if (is_numeric($value)) {
            $value = new Denkmal_Model_Venue($value);
        } else {
            $value = (string) $value;
        }
        return $value;
    }

    /**
     * @param Denkmal_Model_Venue $item
     * @param CM_Frontend_Render  $render
     * @return array
     */
    public function getSuggestion($item, CM_Frontend_Render $render) {
        return array('id' => $item->getId(), 'name' => $item->getName());
    }

    protected function _getSuggestions($term, array $options, CM_Frontend_Render $render) {
        $term = (string) $term;
        $suggestions = array();
        $venueList = new Denkmal_Paging_Venue_All($this->_options['region'], true);
        /** @var $item Denkmal_Model_Venue */
        foreach ($venueList as $item) {
            if (0 === strlen($term) || false !== stripos($item->getName(), $term)) {
                $suggestions[] = $this->getSuggestion($item, $render);
            }
            if (count($suggestions) > 30) {
                break;
            }
        }
        return $suggestions;
    }
}
