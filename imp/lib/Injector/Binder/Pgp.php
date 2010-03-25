<?php
/**
 * Binder for IMP_Crypt_Pgp.
 *
 * Copyright 2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package IMP
 */
class IMP_Injector_Binder_Pgp implements Horde_Injector_Binder
{
    /**
     */
    public function create(Horde_Injector $injector)
    {
        $params = array(
            'program' => $GLOBALS['conf']['gnupg']['path'],
            'temp' => Horde::getTempDir()
        );

        if (isset($GLOBALS['conf']['http']['proxy']['proxy_host'])) {
            $params['proxy_host'] = $GLOBALS['conf']['http']['proxy']['proxy_host'];
            if (isset($GLOBALS['conf']['http']['proxy']['proxy_port'])) {
                $params['proxy_port'] = $GLOBALS['conf']['http']['proxy']['proxy_port'];
            }
        }

        return Horde_Crypt::factory(array('IMP', 'Pgp'), $params);
    }

    /**
     */
    public function equals(Horde_Injector_Binder $binder)
    {
        return false;
    }

}
