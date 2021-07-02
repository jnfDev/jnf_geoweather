<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

class Jnf_Geoweather extends Module
{
    public function __construct()
    {
        $this->name = 'jnf_geoweather';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'JnfDev';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Geo Weather', [], 'Modules.Jnfgeoweather.Jnfgeoweather');
        $this->description = $this->trans('This plugins prints weather information based on visitor location. This plugin is an "admission test" for Interfell.', [], 'Modules.Jnfgeoweather.Jnfgeoweather');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Jnfgeoweather.Jnfgeoweather');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayNav1') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            Configuration::updateValue('JNF_GEOWEATHER_API_KEY', '')
        ;
    }

    public function getGeoWeather($ipAddr = false, $apiKey = false, $lang = false)
    {
        if (!$ipAddr) {
            $ipAddr = Tools::getRemoteAddr();
        }

        if (!$apiKey) {
            $apiKey = Configuration::get('JNF_GEOWEATHER_API_KEY');
        }

        if (!$lang) {
            $lang =  $this->context->language->iso_code;
        }
        
        $client = new WeatherAPILib\WeatherAPIClient($apiKey);
        $aPIs   = $client->getAPIs();

        if (!$apiKey || !($aPIs instanceof WeatherAPILib\Controllers\APIsController)) {
            return false;
        }

        try {
            $result = $aPIs->getRealtimeWeather($ipAddr, $lang);
        } catch (WeatherAPILib\APIException $e) {
            return false;
        }

        return $result;
    }

    /** Hooks */

    public function hookDisplayNav1($params)
    {
        $output = '';
        $geoWeather = $this->getGeoWeather();

        // Uncomment on localhost to test.
        $apiKey = 'e39fcbc2db02436eb1f121433210107';
        $ipAddr = '200.229.216.110';
        $geoWeather = $this->getGeoWeather($ipAddr, $apiKey);

        if (isset($geoWeather->location) && isset($geoWeather->current)) {

            $location  = $geoWeather->location;
            $weather   = $geoWeather->current;
            $condition = $weather->condition;

            $this->context->smarty->assign([
                'location'  => $location->country. ' / ' .$location->name,
                'temp'      => $weather->tempC . '°c / ' .$weather->tempF . '°f',
                'humidity'  => $weather->humidity . '%',
                'condition' => array(
                    'text' => $condition->text,
                    'icon' => $condition->icon,
                ),
            ]);
    
            $output .= $this->display(__FILE__, 'views/templates/hook/displayNav1.tpl');
        }

        return $output;
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        $this->context->controller->registerStylesheet(
            'geoweather-style',
            'modules/'.$this->name.'/views/css/geoweather.css',
            [
            'media' => 'all',
            'priority' => 200,
            ]
        );
    }

    /** Admin Configuration Page */
}