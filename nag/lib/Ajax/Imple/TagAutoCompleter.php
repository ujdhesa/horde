<?php
/**
 * Copyright 2009-2015 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package Nag
 */
class Nag_Ajax_Imple_TagAutoCompleter extends Horde_Core_Ajax_Imple_AutoCompleter
{
    /**
     */
    protected function _getAutoCompleter()
    {
        $opts = array();

        foreach (array('box', 'triggerContainer') as $val) {
            if (isset($this->_params[$val])) {
                $opts[$val] = $this->_params[$val];
            }
        }

        return empty($this->_params['pretty'])
            ? new Horde_Core_Ajax_Imple_AutoCompleter_Ajax($opts)
            : new Horde_Core_Ajax_Imple_AutoCompleter_Pretty($opts);
    }

    /**
     */
    protected function _handleAutoCompleter($input)
    {
        return array_values($GLOBALS['injector']->getInstance('Nag_Tagger')->listTags($input));
    }

}
