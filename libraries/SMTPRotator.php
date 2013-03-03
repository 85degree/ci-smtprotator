<?php
/**
 * Multi SMTP Rotator
 *
 * Codeigniter Library for use to send email from
 * multiple SMTP account. Each message use only one
 * SMTP account.
 */

/**
 * SMTPRotator Class
 *
 * https://github.com/apung/ci-smtprotator
 *
 * Codeigniter Library for use to send email from
 * multiple SMTP account. Each message use only one
 * SMTP account.
 * 
 * @property SMTPRotator $apungmailer
 */
class SMTPRotator
{

    var $host = array();
    var $port = array();
    var $user = array();
    var $pass = array();
    var $security = array();
    var $tablename = 'smtp_servers';
    var $daily_limit = '500';
    var $version = '0.0.1';

    /**
     * SMTPRotator constuctor
     */
    public function __construct(){

        $this->init();
        $ci= &get_instance();
        $ci->load->database();
    }

    /**
     * SMTPRotator::getServers()
     *
     * Get all smtpservers
     * @return array
     */
    public function getServers(){
        $ci = &get_instance();
        $ci->load->database();
        $query = $ci->db->get($this->tablename);
        return $query->result_array();
    }

    /**
     * SMTPRotator::addServer
     *
     * Adding SMTP Server
     *
     * @access public
     * @return void
     */
    public function addServer($config){
        if(isset($config['host'])) {
            $this->host = $config['host'];
        } else {
            die("smtp config host must be defined");
        }
        if(isset($config['port'])) $this->port = $config['port'];
        if(isset($config['user'])) $this->user = $config['user'];
        if(isset($config['pass'])) $this->pass = $config['pass'];
        if(isset($config['security'])) $this->security = $config['security'];

        $ci = &get_instance();
        $ci->load->database();
        $ci->db->insert($this->tablename, $config);

    }


    /**
     * SMTPRotator::getServer
     *
     * Get SMTP Server
     * @return mixed
     */
    public function getServer(){
        $ci = &get_instance();
        $ci->load->database();
        $ci->db->where('count < '.$this->daily_limit);
        $ci->db->order_by('count');
        $query = $ci->db->get($this->tablename);
        return $query->row_array();
    }

    /**
     * SMTPRotator::init()
     * @access protected
     */
    protected function init(){
        $ci = &get_instance();
        $ci->load->database();
        if(!$ci->db->table_exists($this->tablename)){
            $this->_createTable();
        }

        // table preparation
        $this->tableprep();

        // get and set from config
        $ci->config->load('smtp_rotator', TRUE);
        $this->daily_limit = $ci->config->item('daily_limit', 'smtp_rotator');
    }

    /**
     * SMTPRotator::tableprep()
     * @access protected
     */
    protected function tableprep(){
        $ci = &get_instance();
        $set = array(
            'count' => 0,
            'lastsend'=> date("Y-m-d")
        );
        $ci->db->where("lastsend != '".date("Y-m-d")."'");
        $ci->db->update($this->tablename,$set);
    }

    /**
     * SMTPRotator::_createTable()
     * @access protected
     */
    protected function _createTable(){
        $ci = &get_instance();
        $ci->load->database();
        $ci->load->dbforge();
        $fields = array(
            'smtpid' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'host' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'port' => array(
                'type' =>'VARCHAR',
                'constraint' => '100',
                'default' => '25',
            ),
            'user' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'pass' => array(
                'type'=>'VARCHAR',
                'constraint' => '100',
            ),
            'security' => array(
                'type'=>'VARCHAR',
                'constraint'=>'10'
            ),
            'lastsend'=>array(
                'type'=>'DATE',
                'null'=>TRUE
            ),
            'count'=>array(
                'type'=>'TINYINT',
                'constraint'=>'4',
                'default'=>'0'
            )
        );

        $ci->dbforge->add_key('smtpid', TRUE);
        $ci->dbforge->add_field($fields);
        $ci->dbforge->create_table($this->tablename, TRUE);
    }

    /**
     * SMTPRotator::version
     *
     * @return version
     */
    public function version(){
        return $this->version;
    }
}