<?php

namespace UNIVERWP;

/**
 * The main UniverWP class
 * 
 */
class UniverWP
{
    /**
     * The plugin version number.
     *
     * @var string
     * 
     */
    public $version = '0.1.1';

    /**
     * The plugin settings array.
     *
     * @var array
     * 
     */
    public $settings = array();

    /**
     * The plugin data array.
     *
     * @var array
     * 
     */
    public $data = array();

    /**
     * Common object
     *
     * @object
     * 
     */
    public $log;
    public $wpch;
    public $db;

    /**
     * Allowed tags used through the plugin.
     *
     * @var array
     * 
     */
    public $allowed_tags = array(  'input' => array(
                                        'type'  => array('text', 'hidden', 'button'),
                                        'id'    => array(),                                        
                                        'name'  => array(),
                                        'value' => array(),
                                        'style' => array(),
                                        'class' => array()
                                    ),
                                    'div'   => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array()
                                    ),
                                    'p'     => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array()
                                    ),
                                    'pre'   => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array()
                                    ),
                                    'h1'    => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array()
                                    ),
                                    'h3'    => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array()
                                    ),
                                    'a'     => array(
                                                'id'    => array(),                                        
                                                'name'  => array(),
                                                'value' => array(),
                                                'style' => array(),
                                                'class' => array(),
                                                'href'  => array()
                                    )
                                );

    /**
     * Standard __construct
     * 
     * @return  void
     * 
     */
    public function __construct()
    {
        // Load common object
        $this->common();

        // Load Translations
        $this->uwp_load_textdomain();

        // Define settings.
        $this->settings = array(
            'name'                    => __('Universal Extension', 'univerwp'),
            'code'                    => 'main',
            'slug'                    => dirname(UNIVERWP_BASENAME),
            'version'                 => $this->version,
            'basename'                => UNIVERWP_BASENAME,
            'path'                    => UNIVERWP_PATH,
            'file'                    => __FILE__,
            'url'                     => plugin_dir_url(__FILE__)
        );

        if (is_admin()) {
            add_action('admin_menu', array($this, 'main_menu'));
        }

        add_action('admin_enqueue_scripts', array($this, 'uwp_general_js'));
        add_action('admin_enqueue_scripts', array($this, 'uwp_general_css'));
    }

    /**
     * Instantiate common object
     * 
     * @return void
     * 
     */
    public function common()
    {
        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_log.class.php";
        $this->log  = new UniverWP_LOG();

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_wpch.class.php";
        $this->wpch = new UniverWP_WPCH();

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_db.class.php";
        $this->db   = new UniverWP_DB();
    }

    /**
     * Activate the plugin
     * 
     * @return void
     * 
     */    
    public static function activate()
    {
        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_log.class.php";
        $log  = new UniverWP_LOG();

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_wpch.class.php";
        $wpch = new UniverWP_WPCH();

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_db.class.php";
        $db   = new UniverWP_DB();

        // Setup installation table
        $db->_install_table();

        // Add univerwp database version in general option
        add_option('univerwp_db_version', $db->db_version);
        $log->write('main', 'installation', 'SUCCESS', __('univerwp_db_version option added correctly', 'univerwp'));
           
        // Check if the uwp+/- section already exist on wp-config.php

        if ($wpch->check()){
            $log->write('main', 'installation', 'SUCCESS', __('univerwp sections already present on config, no need to do more', 'univerwp'));
        }elseif (!$wpch->set()){
            $log->write('main', 'installation', 'FAILED', __('univerwp problems creating uwp sections on wp-config file. please check', 'univerwp'));
            die();
        }

        // Installation end
        $log->write('main', 'installation', 'SUCCESS', __('univerwp succesfully installed correctly', 'univerwp'));
        return TRUE;
    }

    /**
     * Deactivate the plugin
     *
     * @return void
     * 
     */
    public static function deactivate()
    {
        return TRUE;
    }

    /**
     * Uninstall the plugin
     *
     * @return void
     * 
     */
    public static function uninstall()
    {
        global $wpdb;        

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_wpch.class.php";
        $wpch = new UniverWP_WPCH();

        // Cleanup wp-config.php file
        if (!$wpch->cleanup()){
            die();
        }

        // Uninstall table
        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_db.class.php";
        $db = new UniverWP_DB();
        $db->_uninstall_table();

        // Remove global options
        delete_option('univerwp_db_version');

        return TRUE;
    }

    /**
     * Sets up the UniverWP menu
     * 
     * @return void
     * 
     */
    public function main_menu()
    {
        add_menu_page(
            __('Universal Extension', 'univerwp'),
            'UniverWP',
            'manage_options',
            'univerwp-main',
            array($this, 'main_content'),
            'dashicons-plus-alt',
            20
        );
        add_submenu_page(
            'univerwp-main',
            __('Universal Extension Dashboard', 'univerwp'),
            __('Dashboard', 'univerwp'),
            'manage_options',
            'univerwp-main',
            array($this, 'main_content')
        );
    }

    /**
     * Plugin main dashboard
     * 
     * @return void
     * 
     */
    public function main_content()
    {
        $output = '
        <div class="wrap">
            <h1>'.__('UniverWP - Universal Extension', 'univerwp').'</h1>
            <p>'.__('This is a set of, hopefully, usefull, extension for Wordpress and Woocommerce. We strive to keep it as light as possible. Without any not necessary, code, query and assets. We hope that you like it.', 'univerwp').'</p>
        </div>';
        echo wp_kses($output, $this->allowed_tags);
    }

    /**
     * Enqueue javascript
     * 
     * @return void
     * 
     */
    public function uwp_general_js()
    {
        wp_enqueue_script(
            $this->settings['code'] . '_js',
            plugins_url("assets/admin/js/univerwp.js", UNIVERWP__FILE__),
            array('jquery'),
            TRUE
        );

        wp_enqueue_script(
            $this->settings['code'] . '_simple_notify_js',
            plugins_url("assets/admin/js/vendor/simple-notify/simple-notify.min.js", UNIVERWP__FILE__),
            array(),
            TRUE
        );
    }

    /**
     * Enqueue css
     * 
     * @return void
     * 
     */
    public function uwp_general_css()
    {
        wp_enqueue_style(
            $this->settings['code'].'_style',
            plugins_url("assets/admin/css/univerwp.css", UNIVERWP__FILE__)
        );

        wp_enqueue_style(
            $this->settings['code'] . '_simple_notify_style',
            plugins_url("assets/admin/css/vendor/simple-notify/simple-notify.min.css", UNIVERWP__FILE__)
        );
    }

    /**
     * Load translation
     * 
     * @return void
     * 
     */   
    private function uwp_load_textdomain()
    {	
        load_textdomain( UNIVERWP, WP_PLUGIN_DIR . '/' . dirname( plugin_basename( UNIVERWP__FILE__ ) )."/languages/".UNIVERWP."-".get_locale().".mo");
    }    
}