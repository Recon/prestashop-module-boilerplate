<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Boilerplate code for Prestahsop 1.6 custom modules
 *
 * @link http://doc.prestashop.com/display/PS16/Creating+a+PrestaShop+Module
 */
class ModBoilerplate extends Module
{

    /**
     * @var string
     */
    protected $moduleBaseUrl = '';

    public function __construct()
    {
        $this->name = 'modboilerplate';
        $this->version = '1.0.0';
        $this->author = 'Corneliu Popescu';
        $this->need_instance = true;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        /**
         * administration           Administration
         * advertising_marketing    Advertising & Marketing
         * analytics_stats          Analytics & Stats
         * billing_invoicing        Billing & Invoices
         * checkout                 Checkout
         * content_management       Content Management
         * dashboard                Dashboard
         * emailing                 E-mailing
         * export                   Export
         * front_office_features    Front Office Features
         * i18n_localization        I18n & Localization
         * market_place             Market Place
         * merchandizing            Merchandizing
         * migration_tools          Migration Tools
         * mobile                   Mobile
         * others                   Other Modules
         * payments_gateways        Payments & Gateways
         * payment_security         Payment Security
         * pricing_promotion        Pricing & Promotion
         * quick_bulk_update        Quick / Bulk update
         * search_filter            Search & Filter
         * seo                      SEO
         * shipping_logistics       Shipping & Logistics
         * slideshows               Slideshows
         * smart_shopping           Smart Shopping
         * social_networks          Social Networks
         */
        $this->tab = 'administration';


        parent::__construct();

        $this->displayName = $this->l('Module Boilerplate');
        $this->description = $this->l('Module Boilerplate');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (method_exists('Tools', 'getShopDomainSsl')) {
            $this->moduleBaseUrl = 'https://' . Tools::getShopDomainSsl() . __PS_BASE_URI__ . '/modules/' . $this->name . '/';
        } else {
            $this->moduleBaseUrl = 'https://' . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }

    }

    /**
     * @link http://doc.prestashop.com/display/PS16/Managing+Hooks
     * @return boolean
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!Configuration::get('SOME_CONFIG')) {
            Configuration::set('SOME_CONFIG', 'default value');
        }

        return parent::install() && $this->registerHook('displayFooter');
    }

    /**
     * @return boolean
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * @return string
     */
    public function hookDisplayFooter()
    {
        return $this->display(__FILE__, 'views/templates/hook/displayfooter.tpl');
    }


    /**
     * Creates a configuration page by defining the getContent() method in our module class
     *
     * @link http://doc.prestashop.com/display/PS16/Adding+a+configuration+page
     * @return string
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
            if (!$my_module_name
                || empty($my_module_name)
                || !Validate::isGenericName($my_module_name)
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('MYMODULE_NAME', $my_module_name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    /**
     * @return mixed
     */
    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input'  => [
                [
                    'type'     => 'text',
                    'label'    => $this->l('Configuration value'),
                    'name'     => 'MYMODULE_NAME',
                    'size'     => 20,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' =>
                [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        // Load current value
        $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');

        return $helper->generateForm($fields_form);
    }
}
