<?php

	class NethzAuthModel
	{

		private $ldap_server = 'YOUR-LDAP-SERVER.domain.org';
		private $ldap_basedn = 'YOUR LDAP SEARCH BASE';
		private $ldap_user = array();
		private $message = '';
		
		public function test($username, $password) {
			if( $this->testLdap($username, $password) ) {
				if( $this->testRestrictions($username) ) {
					return true;
				}
				else {
					$this->message = 'You are not allowed to log in!';
					return false;
				}
			}
			else {
				$this->message = 'Authentication failed!';
				return false;
			}
		}

		private function testRestrictions($username) {
			//print_r($this->ldap_user);
			// TODO
			return true;
		}

		private function testLdap($username, $password) {
			$this->link = ldap_connect('ldaps://'.$this->ldap_server);
			$this->cn = 'cn='.$this->ldapQuote($username).','.$this->ldap_basedn;
			
			if( @ldap_bind($this->link, $this->cn, $password) ) {
				$read = ldap_read($this->link, $this->cn, '(objectclass=*)');
				$entries = ldap_get_entries($this->link, $read);
				
				if($entries['count'] == 1) {
					$this->ldap_user = $entries[0];
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}

		private function ldapQuote($string) {
			$string = str_replace(array('\\', '*', '(', ')', ','), array('\5c', '\2a', '\28', '\29', ''), $string);
			for ($i = 0; $i < strlen($string); $i++) {
				$char = substr($string, $i, 1);
				if (ord($char)<32) {
					$hex = dechex(ord($char));
					if (strlen($hex) == 1) $hex = '0' . $hex;
					$string = str_replace($char, '\\' . $hex, $string);
				}
			}
			return $string;
		}

		public function getFullname() {
			if( strlen(@$this->ldap_user['givenname'][0]) && strlen(@$this->ldap_user['sn'][0]) )
				return $this->ldap_user['givenname'][0].' '.$this->ldap_user['sn'][0];
			return '';
		}

		public function getUsermail() {
			if( strlen(@$this->ldap_user['mail'][0]) )
				return $this->ldap_user['mail'][0];
			return '';
		}

		public function getMessage() {
			return $this->message;
		}
	}
?>