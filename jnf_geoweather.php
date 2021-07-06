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
        /**
         * NOTA: Debido a que esta librería ya maneja geo-localización 
         * no fue necesario ocupar la librería interna de Prestashop para obtener la localización.
         */

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

    public function getContent()
    {
        $output = '';
        $errors  = array();

        if (Tools::isSubmit('submit'.$this->name)) {

            $apiKey = strval(Tools::getValue('JNF_GEOWEATHER_API_KEY'));

            if ( empty( $apiKey) ) {
                $errors[] = $this->trans('API Key is required', [], 'Modules.Jnfimporter.Jnfimporter');
            }
        
            if (!count($errors)) {
                Configuration::updateValue('JNF_GEOWEATHER_API_KEY', $apiKey);
                $output .= $this->displayConfirmation($this->trans('Successfully Updated', [], 'Modules.'));
            } else {
                $output .= $this->displayError(implode('<br />', $errors));
            }
        }


        
        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Geo Weather', [], 'Modules.Jnfwelcometext.Jnfwelcometext'),
            ],
            'input' => [
                [
                    'type'  => 'text',
                    'label' => $this->trans('Weather API Key', [], 'Modules.Jnfwelcometext.Jnfwelcometext'),
                    'name'  => 'JNF_GEOWEATHER_API_KEY',
                    'required' => true,
                    'col'  => 5
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.Jnfwelcometext.Jnfwelcometext'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->trans('Save', [], 'Modules.Jnfwelcometext.Jnfwelcometext'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', [], 'Modules.Jnfwelcometext.Jnfwelcometext')
            ]
        ];

        // Load current value0
        $helper->fields_value['JNF_GEOWEATHER_API_KEY'] = Tools::getValue('JNF_GEOWEATHER_API_KEY', Configuration::get('JNF_GEOWEATHER_API_KEY'));

        return $helper->generateForm($fieldsForm);
    }

}