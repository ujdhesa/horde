<?php
/**
 * Copyright 2011-2016 Horde LLC (http://www.horde.org/)
 *
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Service_Weather
 */

/**
 * Horde_Service_Weather_Current_Metar
 *
 * Responsible for parsing encoded METAR text and presenting human readable
 * weather data.
 *
 * Parsing code adapted from PEAR's Services_Weather_Metar class. Original
 * phpdoc attributes as follows:
 * @author      Alexander Wirtz <alex@pc4p.net>
 * @copyright   2005-2011 Alexander Wirtz
 * @link        http://pear.php.net/package/Services_Weather
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @package  Service_Weather
 */
class Horde_Service_Weather_Current_Metar extends Horde_Service_Weather_Current_Base
{
    protected $_map = array(
        'dewpoint' => 'dewPoint',
        'wind_direction' => 'windDirection',
        'wind_degrees' => 'windDegrees',
        'wind_speed' => 'wind',
        'wind_gust' => 'windGust',
        'wind_chill' => 'feltTemperature',
        'temp' => 'temperature',
    );

    /**
     * Compatibility layer for old PEAR/Services_Weather data.
     *
     * @return array  The raw parsed data array - keyed by descriptors that are
     *                compatible with PEAR/Services_Weather.
     */
    public function getRawData()
    {
        $this->_properties['update'] = new Horde_Date($this->_properties['update']);
        return $this->_properties;
    }

    public function __get($property)
    {
        switch ($property) {
        // These are unsupported
        case 'logo_url':
        case 'heat_index':
        case 'icon':
        case 'icon_url':
            return null;
        case 'time':
            return new Horde_Date($this->_properties['update']);
        case 'pressure_trend':
            return !empty($this->_properties['remark']['presschg'])
                ? $this->_properties['remark']['presschg']
                : null;
        case 'condition':
            // @todo - need to build this from other properties.
            return ' TODO ';
            break;
        default:
            if (!empty($this->_properties[$property])) {
                return $this->_properties[$property];
            } elseif (!empty($this->_map[$property])) {
                return $this->_properties[$this->_map[$property]];
            }

            throw new Horde_Service_Weather_Exception_InvalidProperty();
        }
    }

}