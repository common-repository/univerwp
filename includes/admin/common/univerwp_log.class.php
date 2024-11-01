<?php

namespace UNIVERWP;

/**
 * The UniverWP Log (LOG) class
 * 
 */
class UniverWP_LOG
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
     * The plugin logs table
     *
     * @var string
     * 
     */
    public $univerwp_table_logs = 'univerwp_logs';
    
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
        // Define settings.
        $this->module_settings = array(
            'code'  => 'log'
        );      
    }

    /**
     * Write a string on the logs
     *
     * @param string $module [main, rbc, wcf, ...]
     * @param string $operation Category of the operation
     * @param string $status [FAILED,SUCCESS,WARNING]
     * @param [type] $result Long description of the result
     * @return void
     * 
     */
    public function write($module, $operation, $status, $result)
    {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . $this->univerwp_table_logs,
            array(
                'uwp_time' => time(),
                'uwp_module' => $module,
                'uwp_operation' => $operation,
                'uwp_status' => $status,
                'uwp_result' => $result
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
        if ($status=="FAILED" || $status=="WARNING"){
            error_log($module.";".$operation.";".$status.";".$result);
        }
    }
}