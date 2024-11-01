<?php

namespace UNIVERWP;

/**
 * The UniverWP WP DB Handler (DB) class
 * 
 */
class UniverWP_DB
{
    /**
     * The plugin settings array.
     *
     * @var array
     * 
     */
    public $module_settings = array();

    /**
     * The plugin data array.
     *
     * @var array
     * 
     */
    public $data = array();

    /**
     * Storage for class instances.
     *
     * @var array
     * 
     */
    public $instances = array();

    /**
     * The db plugin version number.
     *
     * @var string
     * 
     */
    public $db_version = '0.1.1';

    /**
     * The plugin options table
     *
     * @var string
     * 
     */
    public $univerwp_table_options = 'univerwp_options';

    /**
     * The plugin logs table
     *
     * @var string
     * 
     */
    public $univerwp_table_logs = 'univerwp_logs';

    /**
     * Standard __construct
     *
     * @return  void
     * 
     */
    public function __construct()
    {
        // Load Common components
        $this->common();

        // Define settings
        $this->module_settings = array(
            'code'  => 'univerwp_db'
        );      
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
    }

    /**
     * Install the default table
     * 
     * @return void
     * 
     */
    public function _install_table()
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $_univerwp_table_options = $wpdb->prefix . $this->univerwp_table_options;
        $_univerwp_table_logs = $wpdb->prefix . $this->univerwp_table_logs;
        $_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$_univerwp_table_options'") != $_univerwp_table_options) {
            $sql_options = "CREATE TABLE $_univerwp_table_options (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            uwp_module varchar(10),
            uwp_key varchar(255),
            uwp_value varchar(255),
            PRIMARY KEY  (id),
            UNIQUE KEY uwp_key (uwp_key)
            ) $_collate;";
                        
            dbDelta($sql_options);
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$_univerwp_table_logs'") != $_univerwp_table_logs) {
            $sql_logs = "CREATE TABLE $_univerwp_table_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            uwp_time int(255),
            uwp_module varchar(10),            
            uwp_operation varchar(255),
            uwp_status varchar(255),
            uwp_result varchar(255),            
            PRIMARY KEY  (id)
            ) $_collate;";

            dbDelta($sql_logs);
        }
    }

    /**
     * Install the default table
     * 
     * @return void
     * 
     */
    public function _uninstall_table()
    {
        global $wpdb;

        // Remove the tables
        $_univerwp_table_options = $wpdb->prefix . "univerwp_options";
        $_univerwp_table_logs = $wpdb->prefix . "univerwp_logs";

        $wpdb->query("DROP TABLE IF EXISTS `$_univerwp_table_options`");
        $wpdb->query("DROP TABLE IF EXISTS `$_univerwp_table_logs`");
    }

    /**
     * Write a config in database
     * 
     * @return sql query
     * 
     */
    public function write_config($uwp_key, $uwp_value, $uwp_module_code)
    {
        global $wpdb;

        $sql = "INSERT INTO ".$wpdb->prefix . $this->univerwp_table_options." (uwp_module,uwp_key,uwp_value) VALUES (%s,%s,%s) ON DUPLICATE KEY UPDATE uwp_value = %s";
        $sql = $wpdb->prepare($sql, $uwp_module_code, $uwp_key, $uwp_value, $uwp_value);
        return $wpdb->query($sql);
    }

    /**
     * Read config from database
     * 
     * @return string|number
     * 
     */
    public function read_config($uwp_module_code, $uwp_key)
    {
        global $wpdb;

        $sql = "SELECT uwp_value FROM %i WHERE uwp_module=%s AND uwp_key=%s";
        $sql = $wpdb->prepare($sql, $wpdb->prefix . $this->univerwp_table_options, $uwp_module_code, $uwp_key);
        return $wpdb->get_var($sql);
    }

    /**
     * Return a list of configs
     * 
     * @return array|null
     * 
     */
    public function read_configs($uwp_module_code, $uwp_key)
    {
        global $wpdb;

        $sql = "SELECT * FROM %i WHERE uwp_module=%s AND uwp_key LIKE %s";
        $sql = $wpdb->prepare($sql, $wpdb->prefix . $this->univerwp_table_options, $uwp_module_code, $uwp_key);
        return $wpdb->get_results($sql);
    }

    /**
     * Return a list of configs based on key and value
     * 
     * @return array|null
     * 
     */
    public function read_configs_with_values($uwp_module_code, $uwp_key, $uwp_value)
    {
        global $wpdb;

        $sql = "SELECT * FROM %i WHERE uwp_module=%s AND uwp_key LIKE %s AND uwp_value=%s";
        $sql = $wpdb->prepare($sql, $wpdb->prefix . $this->univerwp_table_options, $uwp_module_code, $uwp_key, $uwp_value);
        return $wpdb->get_results($sql);
    }

    /**
     * Remove config from database
     * 
     * @return int|false
     * 
     */
    public function remove_config($uwp_key, $uwp_module_code)
    {
        global $wpdb;

        return $wpdb->delete($wpdb->prefix . $this->univerwp_table_options, array("uwp_key" => $uwp_key, "uwp_module" => $uwp_module_code));
    }

    /**
     * Check table of the plugin if they are correctly created
     * 
     * @return boolean
     * 
     */
    public function check_table()
    {
        global $wpdb;

        if ( $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '%s'", $wpdb->prefix . $this->univerwp_table_options)) !== $wpdb->prefix . $this->univerwp_table_options || 
             $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '%s'", $wpdb->prefix . $this->univerwp_table_logs)) !== $wpdb->prefix . $this->univerwp_table_logs) {
            return FALSE;
        }
        return TRUE;
    }
}