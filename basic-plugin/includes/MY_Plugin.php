<?php

if( !class_exists( 'MY_Plugin' ) ):

class MY_Plugin {
	
	private
	
		/**
		 *	@type	string
		 */
		$_option_name = '';

	protected

		/**
		 *	The absolute path to this plugin's root directory. That's
		 *	usually just up one level from here...
		 *
		 *	@type	string
		 */
		$basepath = '',
	
		/**
		 *	A list of the action hooks the plugin provides
		 *	@type	array
		 */
		$actions = array(),

		/**
		 *	A list of the filters the plugin provides
		 *	@type	array
		 */
		$filters = array(),

		/**
		 *
		 *	@type	array
		 */
		$options = array();

	/**
	 *	Just doin' a thing.
	 *
	 *	@param	string	(optional) the path to this plugin's root directory
	 */
	public function __construct( $basepath = '' ) {

		if ($basepath == '') {
			// strip the trailing /includes from the current directory
			$basepath = substr(dirname(__FILE__), 0, -9);
		}
	
		$this->basepath = $basepath . '/';
		$this->_option_name = get_class( $this ) . '_options';

		$option = get_option( $this->_option_name );

		if( isset($option) && $option ) {
			$this->options = array_merge($this->options, $option);
		} else {
			$this->save_options($this->options);
		}

		foreach( $this->actions as $action ) {
			add_action( $action, array( $this, $action ) );
		}

		foreach( $this->filters as $filter ) {
			add_filter( $filter, array( $this, $filter ) );
		}
	}

	/**
	 *	Show a view
	 *
	 *	@param	string	the name of the view
	 *	@param	array	(optional) variables to pass to the view
	 *	@param	boolean	echo the view? (default: true)
	 */
	public function load_view( $view, $data = null, $echo = true ) {

		$view = $view . '.php';
		$viewfile = $this->basepath . get_class( $this ) . '/views/' . $view;

		if( !file_exists( $viewfile ) ) {

			$viewfile = $this->basepath . 'views/' . $view;
			
			if( !file_exists( $viewfile ) ) {
				echo 'couldn\'t load view';
			}
		}

		if( is_array( $data ) ) {
			foreach( $data as $key => $value ) {
				${$key} = $value;
			}
		}

		ob_start();
			include $viewfile;
			$result = ob_get_contents();
		ob_end_clean();

		if( $echo ) {
			echo $result;
		} else {
			return $result;
		}
	}

	/**
	 *	Get an option
	 *	@param	string	key
	 *	@return	string 	value
	 */
	protected function get_option ($key) {

		if (array_key_exists($key, $this->options)) {

			$value = $this->options[ $key ];

			if (is_array($value)) {
				return $this->stripslashes_deep($value);
			} elseif (is_string($value)) {
				return stripslashes($value);
			}
		}
		return null;
	}
	
	/**
	 *	Update an option
	 *	@param	string	key
	 *	@param	mixed 	value
	 */
	protected function update_option( $key, $value ) {
	
		$this->options[ $key ] = $value;
		$this->save_options();
	}
	
	/**
	 *	Update a bunch of options en masse
	 *	@param	array	an array containing all of the new options
	 */
	protected function update_options( $instance ) {

		foreach ($this->options as $key => $value) {
			if (isset($instance[$key])) {
				$this->options[$key] = $instance[$key];
			}
		}
		
		$this->save_options();
	}

	/**
	 *	Save options to the database
	 */
	protected function save_options() {
		update_option( $this->_option_name, $this->options );
	}

	/**
	 *	Recursively strips slashes from a variable
	 *	@param	mixed	an array or string to be stripped
	 *	@return	mixed	a "safe" version of the input variable
	 */
	private function stripslashes_deep($value) {
		$value = is_array($value) ?
					array_map(array($this,'stripslashes_deep'), $value) :
					stripslashes($value);

		return $value;
	}
}

endif; // class_exists

?>
