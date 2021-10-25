<?php
class Https {
	public function trigger_https() {
		force_config_ssl();
	}
}