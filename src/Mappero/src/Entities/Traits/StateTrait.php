<?php

namespace App;

use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Exceptions\EntityException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait StateTrait
{


    /**
     * @param string $state
     * @throws EntityException
     */
    public function setState(string $state)
    {
        Entities::hasProperty($this, 'state');
        self::checkStates();
        if (!array_key_exists($state, self::getStates())) {
            throw new EntityException("Unknown state '{$state}'. Available statuses are '" . implode(",", self::getStates()) . "'.");
        }
        $this->state = $state;
    }



    /**
     * @return string
    */
    public function getStateTranslationKey() : string
    {
        $state = $this->getState();
        return self::getStates()[$state]['translationKey'] ?? $state;
    }



    /**
     * @return string|null
     */
    public function getState()
    {
        Entities::hasProperty($this, 'state');
        self::checkStates();
        return $this->state;
    }



    /**
     * @return array
     */
    public static function getStates()
    {
        self::checkStates();
        return self::$states;
    }



    /**
     * Get translated state list.
     * @param $translator ITranslator
     * @return array
     */
    public static function getTranslatedStateList(ITranslator $translator) : array
    {
        self::checkStates();
        $list = [];
        foreach (self::getStates() as $key => $value) {
            $list[$key] = isset($value['translationKey']) ? $translator->translate($value['translationKey']) : $value['translation'];
        }
        return $list;
    }



    /**
     * @param $translator ITranslator|null
     * @return string
    */
    public function getTranslatedState(ITranslator $translator = NULL)
    {
        $states = self::getStates();
        $state = $states[$this->getState()];
        return $translator !== NULL && isset($state['translationKey']) ? $translator->translate($state['translationKey']) : $state['translation'];
    }



    /**
     * @throws EntityException
     */
    protected static function checkStates()
    {
        if (!isset(self::$states) || !is_array(self::$states)) {
            throw new EntityException("Missing array with states. You must create an array with basic structure [state_key => [state_key =>.., translate =>..], state_key=>.. ].");
        }
    }
}