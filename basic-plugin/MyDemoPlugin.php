<?php
/*
Plugin Name: My Demo Plugin
Description: A plugin built on a nice friendly boilerplate
Version: 1.0
Author: RJ Zaworski
Author URI: http://rjzaworski.com
License: JSON
*/

if (!class_exists('MyDemoPlugin')):

require( 'includes/MY_Plugin.php' );

class MyDemoPlugin extends MY_Plugin {

	protected

		// the action hooks this plugin describes
		$actions = array(
			'widgets_init'
		),

		// the default options this plugin set/uses
		$options = array(
			'foo' => 'bar'
		);

	//
	//	Called manually to update the plugin's options
	//
	public function muddle_an_option() {

		if ($this->get_option('foo') == 'bar') {
			$this->update_option('foo', '!bar');
		}
		
		echo $this->get_option('foo'); // !bar
	}

	//
	//	Demonstrate loading a view
	//
	public function test_view() {

		$data = array(
			'message' => 'hello, world!'
		);

		$this->load_view('hello_world', $data);
	}

	//
	//	Called by the WP 'widgets_init' hook
	//
	public function widgets_init() {
		$this->muddle_an_option();
		$this->test_view();
	}
}

new MyDemoPlugin();

endif; // class_exists

?>