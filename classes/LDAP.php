<?php 

class LDAP
{
	private $_server1 = NULL;
	private $_server2 = NULL;
	private $_port = NULL;
	private $_base_dn = NULL;
	private $_link_identifier = NULL;
	private $_result_identifier = NULL;
	private $_ldap_link_resource_type = 'ldap link';
	private $_entries = array();
	
	private function __construct($server1,$server2,$port,$base_dn){
		$this->_server1 = $server1;
		$this->_server2 = $server2;
		$this->_port = $port;
		$this->_base_dn = $base_dn;
		$this->_ldapConnect();
	}
	
	public function __destruct() {
		if ( $this->_has_ldap_link() ) {
			ldap_close($this->_link_identifier); 
		}
	}
	
	public static function connect($server1=NULL,$server2=NULL,$port=NULL,$base_dn=NULL){
		if ( is_string($server1) && is_string($server2) && is_int($port) &&  is_string($base_dn) ) {
			return new LDAP($server1,$server2,$port,$base_dn);
		} else {
			return FALSE;
		}
	}
	
	// see: http://us2.php.net/manual/en/function.ldap-connect.php
	// if using open ldap, ldap_connect always returns resource
	// after connect use bind to test the connect works and return resource/FALSE
	private function _ldapConnect(){
		$resource = ldap_connect($this->_server1,$this->_port);
		if ( @ldap_bind($resource) ) {
			$this->_link_identifier = $resource;
		} else {
			$resource=ldap_connect($this->_server2);
			if ( @ldap_bind($resource) ) {
				$this->_link_identifier = $resource;
			} else {
				return FALSE;
			}
			return FALSE;
		}
	}
	
	public function ldapSearch($filter=NULL, $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=1){
		if ( $this->_has_ldap_link() && is_string($this->_base_dn) && is_string($filter) && is_array($attributes) && is_int($attrsonly) && is_int($sizelimit) && is_int($timelimit) && is_int($deref) ) {
			if ( $result_identifier = ldap_search($this->_link_identifier, $this->_base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref) ) {
				$this->_result_identifier = $result_identifier;
				if ( $entries = ldap_get_entries($this->_link_identifier, $this->_result_identifier) ) {
					$this->_entries = $entries;
					return $this->_entries['count'];
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	public function ldapAuthenticate($bind_rdn=NULL, $bind_password=NULL){
		if ( is_string($bind_rdn) && is_string($bind_password) ) {
			if ( $bind_rdn === '' ) {
				return FALSE;
			}
			if ( $bind_password === '' ) {
				return FALSE;
			}
			return $this->_ldapBind($bind_rdn, $bind_password);
		} else {
			return FALSE;
		}
	}
	
	public function ldapGetDn($filter=NULL,$attribute=NULL){
		if ( is_string($filter) && is_string($attribute) ) {
			$entries = $this->ldapSearch($filter, array($attribute));
			if ( isset($this->_entries[0][$attribute]) ) {
				return $this->_entries[0][$attribute];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	private function _ldapBind($bind_rdn=NULL, $bind_password=NULL){
		if ( $this->_has_ldap_link() && is_string($bind_rdn) && is_string($bind_password) ) {
			return @ldap_bind($this->_link_identifier,$bind_rdn,$bind_password); 
		} else {
			return FALSE;
		}
	}
	
	private function _has_ldap_link(){
		if ( is_resource($this->_link_identifier) && get_resource_type($this->_link_identifier) === $this->_ldap_link_resource_type ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getEntries(){
		return $this->_entries;
	}
}
?>
