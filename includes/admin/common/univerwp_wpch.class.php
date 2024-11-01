<?php

namespace UNIVERWP;

/**
 * The UniverWP WP Config Handler (WPCH) class
 * 
 */
class UniverWP_WPCH
{
    /**
     * The plugin settings array.
     *
     * @var array
     * 
     */
    public $module_settings = array();

    /**
     * Storage for class instances.
     *
     * @var array
     * 
     */
    public $instances = array();

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

        // Define settings
        $this->module_settings = array(
            'code'  => 'wpch'
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

        require_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/common/univerwp_db.class.php";
        $this->db   = new UniverWP_DB();
    }

    /**
     * Backup the wp-config.php before to modify it
     * true if ok false otherwise
     * 
     * @return boolean
     * 
     */
    public function backup($operation)
    {
        switch($operation){
            case 'create':
                if (!copy(ABSPATH . "wp-config.php", ABSPATH . "_wp-config.php")) {
                    $this->log->write($this->module_settings['code'], 'create-backup', 'FAILED', __('creating temporary _wp-config.php before to create our slot', 'univerwp'));
                    return FALSE;
                }
                $this->log->write($this->module_settings['code'], 'create-backup', 'SUCCESS', __('creating temporary _wp-config.php before to create our slot', 'univerwp'));
                return TRUE;
            break;
            case 'recover':
                if (!copy(ABSPATH . "_wp-config.php", ABSPATH . "wp-config.php")) {
                    $this->log->write($this->module_settings['code'], 'recover-backup', 'FAILED', __('recover _wp-config.php to wp-config.php please check', 'univerwp'));
                    return FALSE;
                }
                $this->log->write($this->module_settings['code'], 'recover-backup', 'SUCCESS', __('recover _wp-config.php to wp-config.php', 'univerwp'));
                return TRUE;
            break;
            case 'remove':
                if (!unlink(ABSPATH . "_wp-config.php")){
                    $this->log->write($this->module_settings['code'], 'remove-backup', 'FAILED', __('removing temporary _wp-config.php after creating uwp slot', 'univerwp'));
                    return FALSE;
                }
                $this->log->write($this->module_settings['code'], 'remove-backup', 'SUCCESS', __('removing temporary _wp-config.php after creating uwp slot', 'univerwp'));
            break;
        }
    }

    /**
     * Set the uwp+/- section in wp-config.php
     * true if ok false otherwise
     * 
     * @return boolean
     * 
     */
    public function set()
    {
        // Backup first
        if (!$this->backup('create')){
            return FALSE;
        }

        // Check if the file wp-config.php is writable
        if (!is_writable(ABSPATH . "wp-config.php")) {
            $this->log->write($this->module_settings['code'], 'check-write', 'FAILED', __('wp-config.php is not writable', 'univerwp'));
            return FALSE;
        }
        $this->log->write($this->module_settings['code'], 'check-write', 'SUCCESS', __('wp-config.php is writable', 'univerwp'));
        
        // Create the uwp+/- slot of wp-config.php
        $wpconfig = str_replace("<?php", "<?php \n/* uwp+ */ \n/* uwp- */\n", file_get_contents(ABSPATH . "/wp-config.php"));
        if (!file_put_contents(ABSPATH . "wp-config.php", $wpconfig)){
            $this->log->write($this->module_settings['code'], 'write', 'FAILED', __('writing uwp+/- section in wp-config.php', 'univerwp'));
            // Recover backup
            $this->backup('recover');
            return FALSE;
        }
        $this->log->write($this->module_settings['code'], 'write', 'SUCCESS', __('writing uwp+/- section in wp-config.php', 'univerwp'));

        // Remove backup
        $this->backup('remove');
        return TRUE;
    }

    /**
     * Update the wp-config.php file
     * true if ok, false otherwise
     * 
     * @return boolean
     * 
     */
    public function update($code, $data)
    {
        $array_wpconfig = file(ABSPATH . "wp-config.php");
        $support_wpconfig = array();
        $in_uwp_section = 0;
        $override_done = 0;

        $fetch_configs = $this->db->read_configs($code, 'config_%');

        // Backup first
        if (!$this->backup('create')){
            return FALSE;
        }

        foreach ($array_wpconfig as $_row) {
            if (($in_uwp_section == 0) && (strpos($_row, "uwp+") == FALSE)) {
                array_push($support_wpconfig, $_row);
            }
            if (strpos($_row, "uwp+") !== FALSE) {
                $in_uwp_section = 1;
                array_push($support_wpconfig, $_row);
                continue;
            }
            if (($in_uwp_section == 1) && ($override_done == 0)) {
                foreach ($fetch_configs as $_config) {
                    array_push($support_wpconfig, $this->make_string($_config->uwp_key, $_config->uwp_value, $data) . PHP_EOL);
                    $override_done = 1;
                }
            }
            if (strpos($_row, "uwp-") !== FALSE) {
                $in_uwp_section = 0;
                array_push($support_wpconfig, $_row);
            }
        }

        if (!file_put_contents(ABSPATH . "wp-config.php", $support_wpconfig)){
            $this->log->write($this->module_settings['code'], 'update-wpconfig', 'FAILED', __('problem to update the wp-config.php', 'univerwp'));
            // Recover backup
            $this->backup('recover');
            return FALSE;
        }

        $this->log->write($this->module_settings['code'], 'update-wpconfig', 'SUCCESS', __('correctly update the wp-config.php', 'univerwp'));

        // Remove backup
        $this->backup('remove');
        return TRUE;
    }

    /**
     * Check if the uwp+/- is already present on wp-config.php file
     * true if found, false otherwise
     * 
     * @return boolean
     * 
     */
    public function check()
    {
        $wpconfig = file_get_contents(ABSPATH . "wp-config.php");

        if (strpos($wpconfig, "uwp+") === FALSE) {
            $this->log->write($this->module_settings['code'], 'search', 'WARNING', __('uwp+/- section not found in wp-config.php', 'univerwp'));
            return FALSE;
        }

        $this->log->write($this->module_settings['code'], 'search', 'SUCCESS', __('uwp+/- section found in wp-config.php no need to add them', 'univerwp'));
        return TRUE;
    }

    /**
     * Check if a key is already present on the wp-config.php file
     * true if found, false otherwise
     * 
     * @return boolean
     * 
     */
    public function check_key($key)
    {
        $array_wpconfig = file(ABSPATH . "wp-config.php");
        $support_wpconfig = array();
        $in_uwp_section = 0;
        $override_done = 0;

        foreach ($array_wpconfig as $_row) {
            if (($in_uwp_section == 0) && (strpos($_row, "uwp+") == FALSE)) {
                array_push($support_wpconfig, $_row);
            }
            if (strpos($_row, "uwp+") !== FALSE) {
                $in_uwp_section = 1;
                array_push($support_wpconfig, $_row);
                continue;
            }
            if (strpos($_row, "uwp-") !== FALSE) {
                $in_uwp_section = 0;
                array_push($support_wpconfig, $_row);
            }
        }
                
        if (strpos(implode($support_wpconfig), $key)){
            $this->log->write($this->module_settings['code'], 'search-key', 'FAILED', __('key already present in wp-config.php no need to recreate them', 'univerwp'));
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Cleanup uwp+/- section from wp-config.php
     * 
     * @return boolean
     * 
     */
    public function cleanup()
    {
        $_pattern = '/\/\* uwp\+(.*)uwp\- \*\//s';
        $_replacement = '';

        // Backup first
        if (!$this->backup('create')){
            return FALSE;
        }

        $_wpconfig = preg_replace($_pattern, $_replacement, file_get_contents(ABSPATH . "wp-config.php"));

        if (!file_put_contents(ABSPATH . "wp-config.php", $_wpconfig)){
            $this->log->write('main', $this->module_settings['code'], 'FAILED', __('problem to update the wp-config.php after removing of uwp+/- section', 'univerwp'));
            // Recover backup
            $this->backup('recover');
            return FALSE;
        }

        $this->log->write('main', $this->module_settings['code'], 'SUCCESS', __('correctly update wp-config.php after removing of uwp+/- section', 'univerwp'));
        // Remove backup
        $this->backup('remove');
        return TRUE;
    }

    /**
     * Return a string to write on the config or to run a filter
     * 
     * @return string
     * 
     */
    private function make_string($uwp_key, $uwp_value, $data)
    {
        $config = explode("_", $uwp_key)[1];
        $data = $data[$config];

        // fix here
        
        switch($data["type"]){
            case 'constant':
                return sprintf("define( '".$data["wp_config_label"]."', '%s' );", $uwp_value);
            break;
            case 'variable':
                // 
            break;
            case 'filter':
                return $data["wp_config_label"];
            break;
        }
    }
}