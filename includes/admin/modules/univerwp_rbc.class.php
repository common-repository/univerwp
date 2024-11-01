<?php

namespace UNIVERWP;

/**
 * The UniverWP Remove Bloated Configuration (RBC) class
 * 
 */
class UniverWP_RBC extends UniverWP
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
     * type               => variable | constant
     * value_type         => boolean | int
     * code               => internal code
     * category           => config | filter
     * field_label'       => label of the field
     * title'             => title of this setting
     * wp_config_label'   => wordpress config label
     * description'       => description of the field
     * already_set'       => message if the field is already set on wp-config
     * enabled_notice'    => message if the field is set with univerwp
     * enabled_more'      => additional description if the field is set with univerwp
     * label_btn_on'      => label of the button on
     * label_btn_off'     => label of the button off
     * 
     */
    public $data = array();

    /**
     * Storage for class instances
     *
     * @var array
     * 
     */
    public $instances = array();

    /**
     * Standard __construct
     *
     * @return void
     * 
     */
    public function __construct()
    {
        // Load common Object
        $this->common();

        // Define settings
        $this->module_settings = array(            
                'code'         => 'rbc'
        );

        $this->data['revision'] = [
                'code'              => 'revision',
                'category'          => 'config',
                'type'              => 'constant',
                'value_type'        => 'int',
                'field_label'       => __('Limit revision to', 'univerwp'),
                'title'             => __('Limit Revision', 'univerwp'),
                'wp_config_label'   => 'WP_POST_REVISIONS',
                'description'       => __('By default WordPress does not limit the number of revision for your post. This can increase the space of your database very quick (and then slowdown the query over it). We suggest to limit it.', 'univerwp'),
                'already_set'       => __('You have already some custom revision set on your wp-config.php file. Please remove it, if you want to manage with UniverWP.', 'univerwp'),
                /* translators: the parameter will be the number of revisions. int */
                'enabled_notice'    => __('<b>wordpress revision limit enabled</b>. The actual limit revision used is: <b><span id="revision_actual_value">%s</span></b>', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Update Revision Limit', 'univerwp'),
                'label_btn_off'     => __('Reset Revision Limit', 'univerwp'),
        ];
        $this->data['wpcrondisable'] = [                
                'code'              => 'wpcrondisable',
                'category'          => 'config',
                'type'              => 'constant',
                'value_type'        => 'boolean',              
                'field_label'       => '',
                'title'             => __('Disable Wordpress Cron', 'univerwp'),
                'wp_config_label'   => 'DISABLE_WP_CRON',
                'description'       => __('By default Wordpress has an in bundle cron system that fires up, every times a user load a page. This could become a problem very quick if the traffic of your website increase. Generally speaking is far better to relay to the system cron, that nowadays every decent hosting provider give you for free. Generally fires up a cronjob every 30 minutes is enough for most.', 'univerwp'),
                'already_set'       => __('You have already disabled Wordpress default cron on wp-config.php file. Please remove it, if you want to manage it with UniverWP.', 'univerwp'),
                'enabled_notice'    => __('<b>WordPress Cron disabled</b>. Please be sure to insert on your system cron one of the following line (depeding on what is available on your hosting).', 'univerwp'),
                'enabled_more'      => '<pre>
# wget
*/30 * * * * wget -q -O - https://' . get_site_url() . '/wp-cron.php?doing_wp_cron
# curl
*/30 * * * * curl https://' . get_site_url() . '/wp-cron.php?doing_wp_cron > /dev/null 2>&1
# php-cli
*/30 * * * * cd '. ABSPATH . '; php wp-cron.php > /dev/null 2>&1
# wp-cli
*/30 * * * * cd ' . ABSPATH . '; wp cron event run --due-now > /dev/null 2>&1
</pre>',
                'label_btn_on'      => __('Disable WordPress Cron', 'univerwp'),
                'label_btn_off'     => __('Reset WordPress Cron', 'univerwp'),
        ];
        $this->data['wpautoupdate'] = [                
                'code'              => 'wpautoupdate',
                'category'          => 'config',
                'type'              => 'constant',
                'value_type'        => 'boolean',      
                'field_label'       => '',
                'title'             => __('Disable WordPress Automatic Updates', 'univerwp'),
                'wp_config_label'   => 'AUTOMATIC_UPDATER_DISABLED',
                'description'       => __('Keep your website (all the parts of it: core WordPress, all the plugins, the themes and the OS parts) updated, is vitally important. But, before every upgrades, is far better to make a snapshot/backup of your website (both files and database dump). To do that, is better disable the automatic updates of WordPress, in order to allow you to decide when upgrade your website.', 'univerwp'),
                'already_set'       => __('You have already disabled Wordpress automatic update on wp-config.php file. Please remove it, if you want to manage with UniverWP.', 'univerwp'),
                'enabled_notice'    => __('<b>WordPress Automatic Updates disabled</b>. Please be sure to manually update your WordPress regulary, and before to do that make a complete backup of it.', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Disable Automatic Update', 'univerwp'),
                'label_btn_off'     => __('Reset Automatic Update', 'univerwp'),
        ];
        $this->data['fileditor'] = [                
                'code'              => 'fileditor',
                'category'          => 'config',
                'type'              => 'constant',
                'value_type'        => 'boolean',              
                'field_label'       => '',
                'title'             => __('Disable WordPress Theme File Editor', 'univerwp'),
                'wp_config_label'   => 'DISALLOW_FILE_EDIT',
                'description'       => __('This flag allow you to disable the useless Theme File editor present on the default Wordpress.', 'univerwp'),
                'already_set'       => __('You have already disabled Wordpress Theme File editor on wp-config.php file. Please remove it, if you want to manage with UniverWP.', 'univerwp'),
                'enabled_notice'    => __('<b>WordPress Theme File Editor disabled</b>', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Disable Theme File editor', 'univerwp'),
                'label_btn_off'     => __('Reset Theme File editor', 'univerwp'),
        ];
        $this->data['plugineditor'] = [
                'code'              => 'plugineditor',
                'category'          => 'config',
                'type'              => 'constant',
                'value_type'        => 'boolean',
                'field_label'       => '',
                'title'             => __('Disable WordPress Plugin File editor', 'univerwp'),
                'wp_config_label'   => 'DISALLOW_FILE_MODS',
                'description'       => __('This flag allow you to disable the useless Plugin File editor present on the default Wordpress.', 'univerwp'),
                'already_set'       => __('You have already disabled Wordpress Plugin File editor on wp-config.php file. Please remove it, if you want to manage with UniverWP.', 'univerwp'),
                'enabled_notice'    => __('<b>WordPress Plugin File Editor disabled</b>', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Disable Plugin File editor', 'univerwp'),
                'label_btn_off'     => __('Reset Plugin File editor', 'univerwp'),
        ];
        $this->data['disablegutemberg'] = [
                'code'              => 'disablegutemberg',
                'category'          => 'filter',
                'type'              => '',
                'value_type'        => 'boolean',
                'field_label'       => base64_encode("add_filter('use_block_editor_for_post', '__return_false', 10);"),
                'title'             => __('Disable WordPress Gutemberg Editor', 'univerwp'),
                'wp_config_label'   => '',
                'description'       => __('This flag allow you to disable the Gutemberg Editor.', 'univerwp'),
                'already_set'       => '',
                'enabled_notice'    => __('<b>WordPress Gutemberg Editor is disabled</b>', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Disable Gutemberg Editor', 'univerwp'),
                'label_btn_off'     => __('Reset Gutemberg Editor', 'univerwp'),
        ];
        $this->data['disablewidgetblockeditor'] = [
                'code'              => 'disablewidgetblockeditor',
                'category'          => 'filter',
                'type'              => '',
                'value_type'        => 'boolean',
                'field_label'       => base64_encode("add_action( 'after_setup_theme', function () {remove_theme_support( 'widgets-block-editor' );});"),
                'title'             => __('Disable WordPress Widget Block Editor', 'univerwp'),
                'wp_config_label'   => '',
                'description'       => __('This flag allow you to disable the Widget Block Editor.', 'univerwp'),
                'already_set'       => '',
                'enabled_notice'    => __('<b>WordPress Widget Block Editor is disabled</b>', 'univerwp'),
                'enabled_more'      => '',
                'label_btn_on'      => __('Disable Widget Block Editor', 'univerwp'),
                'label_btn_off'     => __('Reset Widget Block Editor', 'univerwp'),
        ];
        
        if (is_admin()) {
            // Admin only
            add_action('admin_menu', array($this, 'menu'));
            add_action('admin_enqueue_scripts', array($this, 'init'));
            add_action('wp_ajax_rbc_update', [$this, 'rbc_update']);            

            // Run filter set only if the tables of the plugin are set
            if ($this->db->check_table()) {
                $this->run_filter();
            }
        }
    }

    /**
     * Sets up the UniverWP RBC Module plugin
     * 
     * @return void
     * 
     */
    public function init()
    {
        wp_enqueue_script(
            $this->module_settings['code'] . '_ajax_dashboard_script',
            plugins_url("assets/admin/js/univerwp_rbc.js", UNIVERWP__FILE__),
            array('jquery'),
            TRUE
        );
        wp_localize_script(
            $this->module_settings['code'].'_ajax_dashboard_script',
            'rbcXHR',
            array(
                'url'   => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce("rbc_update_nonce"),
            )
        );
    }

    /**
     * Sets up the UniverWP menu
     * 
     * @return void
     * 
     */
    public function menu()
    {
        add_submenu_page(
            'univerwp-main',
            __('Remove Bloated Configurations', 'univerwp'),
            __('Bloated Config', 'univerwp'),
            'manage_options',
            'univerwp-rbc',
            array($this, 'content')
        );
    }

    /**
     * Main module content output
     * 
     * @return string
     * 
     */
    public function content()
    {
        $actual_value = "";
        $tpl_header = '<div class="wrap"><h1>'.__('UniverWP Remove Bloated Configuration', 'univerwp'). '</h1><p>'.__('Here we suggest some configuration to make your Wordpress faster', 'univerwp').'</p><hr>';
        $tpl_body = '';

        foreach($this->data as $config){
            $tpl_body .= '<h3 class="title">'.$config['title'].'</h3>';

            if ($this->wpch->check_key($config['wp_config_label'])){
                $tpl_body .= '<div class="uwp_error">';
                $tpl_body .= '<p>'.$config['already_set'].'</p>';
                $tpl_body .= '</div>';
            }else{
                $actual_value = $this->db->read_config($this->module_settings['code'], $config['category'].'_'.$config['code']);

                $tpl_body .= '<div class="description">';
                $tpl_body .= '  <p>'.$config['description'].'</p>';
                $tpl_body .= '</div>';
                $tpl_body .= '<div class="'.$config['code'].'_off ' . ($actual_value ? '' : 'uwp_hide') . '">';
                $tpl_body .= '  <div class="uwp_notice">';
                $tpl_body .= '    <p>'.sprintf($config['enabled_notice'], ($actual_value ? $actual_value : '')).'</p>';
                $tpl_body .= '  </div>';
                $tpl_body .= '  <div class="more_description">';
                $tpl_body .= '    <p>'.$config['enabled_more'].'</p>';
                $tpl_body .= '  </div>';
                $tpl_body .= '</div>';                
                if ($config['value_type'] != 'boolean'){
                    $tpl_body .= '<p>'.$config['field_label'].': <input type="text" class="small-text" name="'.$config['code'].'_field_value" id="'.$config['code'].'_field_value" value="'. ($actual_value ? $actual_value : '') .'"></p>';
                }
                $tpl_body .= "<input type='hidden' name='".$config['code']."_data' id='".$config['code']."_data' value='".base64_encode(json_encode($config))."'>";
                $tpl_body .= '<input type="button" class="button button-info action '.$config['code'].' '.$config['code'].'_off ' . ($actual_value ? '' : 'uwp_hide') . '" id="'.$config['code'].'_off" value="'.$config['label_btn_off'].'"> ';
                $tpl_body .= '<input type="button" class="button button-primary action '.$config['code'].'" id="'.$config['code'].'_on" value="'.$config['label_btn_on'].'"> ';
            }
        }

        $tpl_footer = '</div>';
        echo wp_kses($tpl_header.$tpl_body.$tpl_footer, $this->allowed_tags);
    }

    /**
     * Manage module XHR request
     * 
     * @return boolean
     * 
     */
    public function rbc_update()
    {
        // Sanatize _POST value
        $action = sanitize_text_field($_POST['action']);
        $field  = sanitize_text_field($_POST['field']);
        $value  = sanitize_text_field($_POST['value']);
        $state  = sanitize_text_field($_POST['state']);

        // Validate ajax nonce
        if (check_ajax_referer('rbc_update_nonce', 'nonce') === FALSE) {
            wp_send_json_error(__('Invalid request, please retry. [rbc_e0]', 'univerwp'));
            wp_die();
            return FALSE;
        }        

        // Validate _POST value
        if ($action != "rbc_update"){
            wp_send_json_error(__('Invalid request, please retry. [rbc_e1]', 'univerwp'));
            wp_die();
            return FALSE;
        }
        if (!array_key_exists($field, $this->data)){
            wp_send_json_error(__('Invalid request, please retry. [rbc_e2]', 'univerwp'));
            wp_die();
            return FALSE;
        }
        if ($value != "x" && $value != "false" && $value != "true" && !is_numeric($value)){
            wp_send_json_error(__('Invalid request, please retry. [rbc_e3]', 'univerwp'));
            wp_die();
            return FALSE;
        }
        if ($state != "on" && $state != "off"){
            wp_send_json_error(__('Invalid request, please retry. [rbc_e4]', 'univerwp'));
            wp_die();
            return FALSE;
        }

        // Get Field data
        $data   = $this->data[$field];
      
        if ($value == "x" || $value == "false"){
            if ($this->db->remove_config($data['category'].'_'.$field, $this->module_settings['code']) === FALSE) {
                $this->log->write($this->module_settings['code'], 'remove_config', 'FAILED', sprintf(__('failed to remove config_%s from uwp_options table'), $field));
                wp_send_json_error(__('Failed to remove the config from uwp_options table', 'univerwp'));
                wp_die();
                return FALSE;
            }
            $this->log->write($this->module_settings['code'], 'remove_config', 'SUCCESS', sprintf(__('correctly removed config_%s from uwp_options table'), $field));
        }elseif (is_numeric($value) || $value == "true"){
            if ($this->db->write_config($data['category'].'_'.$field, $value, $this->module_settings['code']) === FALSE) {
                $this->log->write($this->module_settings['code'], 'mod_config', 'FAILED', sprintf(__('failed to write config_%s from uwp_options table'), $field));
                wp_send_json_error(__('Failed to write the config from uwp_options table', 'univerwp'));
                wp_die();
                return FALSE;
            }
            $this->log->write($this->module_settings['code'], 'mod_config', 'SUCCESS', sprintf(__('correctly write config_%s from uwp_options table'), $field));
        }else{
            wp_send_json_error(__('Invalid value, please retry.', 'univerwp'));
            wp_die();
            return FALSE;
        }

        if ($this->wpch->update($this->module_settings['code'], $this->data)){
            wp_send_json_success(__('Config correctly updated.', 'univerwp'));
        }else{
            wp_send_json_error(__('Problems updating config file. Please check the permission.', 'univerwp'));
        }
        
        wp_die();
        return TRUE;
    }    

    /**
     * Exec a filter enabled by the customer
     * 
     * @return void
     * 
     */
     private function run_filter()
     {
        $fetch_filters = $this->db->read_configs_with_values($this->module_settings['code'], 'filter_%', 'true');

        foreach ($fetch_filters as $_filter) {
            $filter = explode("_", $_filter->uwp_key)[1];
            $_run = base64_decode($this->data[$filter]['field_label']);
            eval($_run);
        }
     }
}