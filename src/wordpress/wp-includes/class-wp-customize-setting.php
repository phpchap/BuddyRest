<?php
/**
 * Customize Setting Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

class WP_Customize_Setting {
	public $manager;
	public $id;

	public $type              = 'theme_mod';
	public $capability        = 'edit_theme_options';
	public $theme_supports    = '';
	public $default           = '';
	public $sanitize_callback = '';
	public $transport         = 'refresh';

	protected $id_data = array();
	private $_post_value; // Cached, sanitized $_POST value.

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id An specific ID of the setting. Can be a
	 *                   theme mod or option name.
	 * @param array $args Setting arguments.
	 */
	function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_class_vars( __CLASS__ ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->id = $id;

		// Parse the ID for array keys.
		$this->id_data[ 'keys' ] = preg_split( '/\[/', str_replace( ']', '', $this->id ) );
		$this->id_data[ 'base' ] = array_shift( $this->id_data[ 'keys' ] );

		// Rebuild the ID.
		$this->id = $this->id_data[ 'base' ];
		if ( ! empty( $this->id_data[ 'keys' ] ) )
			$this->id .= '[' . implode( '][', $this->id_data[ 'keys' ] ) . ']';

		if ( $this->sanitize_callback != '' )
			add_filter( "customize_sanitize_{$this->id}", $this->sanitize_callback );

		return $this;
	}

	/**
	 * Handle previewing the setting.
	 *
	 * @since 3.4.0
	 */
	public function preview() {
		switch( $this->type ) {
			case 'theme_mod' :
				add_filter( 'theme_mod_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				break;
			case 'option' :
				if ( empty( $this->id_data[ 'keys' ] ) )
					add_filter( 'pre_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				else
					add_filter( 'option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
				break;
			default :
				do_action( 'customize_preview_' . $this->id );
		}
	}

	/**
	 * Callback function to filter the theme mods and options.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed Old value.
	 * @return mixed New or old value.
	 */
	public function _preview_filter( $original ) {
		return $this->multidimensional_replace( $original, $this->id_data[ 'keys' ], $this->post_value() );
	}

	/**
	 * Set the value of the parameter for a specific theme.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if cap check fails or value isn't set.
	 */
	public final function save() {
		$value = $this->post_value();

		if ( ! $this->check_capabilities() || ! isset( $value ) )
			return false;

		do_action( 'customize_save_' . $this->id_data[ 'base' ] );

		$this->update( $value );
	}

	/**
	 * Fetches, validates, and sanitizes the $_POST value.
	 *
	 * @since 3.4.0
	 *
	 * @param $default mixed A default value which is used as a fallback. Default is null.
	 * @return mixed Either the default value on failure or sanitized value.
	 */
	public final function post_value( $default = null ) {
		if ( isset( $this->_post_value ) )
			return $this->_post_value;

		$result = $this->manager->post_value( $this );

		if ( isset( $result ) )
			return $this->_post_value = $result;
		else
			return $default;
	}

	/**
	 * Sanitize an input.
	 *
	 * @since 3.4.0
	 *
	 * @param $value mixed The value to sanitize.
	 * @return mixed Null if an input isn't valid, otherwise the sanitized value.
	 */
	public function sanitize( $value ) {
		$value = stripslashes_deep( $value );
		return apply_filters( "customize_sanitize_{$this->id}", $value );
	}

	/**
	 * Set the value of the parameter for a specific theme.
	 *
	 * @since 3.4.0
	 *
	 * @param $value mixed The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function update( $value ) {
		switch( $this->type ) {
			case 'theme_mod' :
				return $this->_update_theme_mod( $value );
				break;
			case 'option' :
				return $this->_update_option( $value );
				break;
			default :
				return do_action( 'customize_update_' . $this->type, $value );
		}
	}

	/**
	 * Update the theme mod from the value of the parameter.
	 *
	 * @since 3.4.0
	 *
	 * @param $value mixed The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function _update_theme_mod( $value ) {
		// Handle non-array theme mod.
		if ( empty( $this->id_data[ 'keys' ] ) )
			return set_theme_mod( $this->id_data[ 'base' ], $value );

		// Handle array-based theme mod.
		$mods = get_theme_mod( $this->id_data[ 'base' ] );
		$mods = $this->multidimensional_replace( $mods, $this->id_data[ 'keys' ], $value );
		if ( isset( $mods ) )
			return set_theme_mod( $this->id_data[ 'base' ], $mods );
	}

	/**
	 * Update the theme mod from the value of the parameter.
	 *
	 * @since 3.4.0
	 *
	 * @param $value mixed The value to update.
	 * @return mixed The result of saving the value.
	 */
	protected function _update_option( $value ) {
		// Handle non-array option.
		if ( empty( $this->id_data[ 'keys' ] ) )
			return update_option( $this->id_data[ 'base' ], $value );

		// Handle array-based options.
		$options = get_option( $this->id_data[ 'base' ] );
		$options = $this->multidimensional_replace( $options, $this->id_data[ 'keys' ], $value );
		if ( isset( $options ) )
			return update_option( $this->id_data[ 'base' ], $options );
	}

	/**
	 * Fetch the value of the parameter for a specific theme.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The requested value.
	 */
	public function value() {
		switch( $this->type ) {
			case 'theme_mod' :
				$function = 'get_theme_mod';
				break;
			case 'option' :
				$function = 'get_option';
				break;
			default :
				return apply_filters( 'customize_value_' . $this->id_data[ 'base' ], $this->default );
		}

		// Handle non-array value
		if ( empty( $this->id_data[ 'keys' ] ) )
			return $function( $this->id_data[ 'base' ], $this->default );

		// Handle array-based value
		$values = $function( $this->id_data[ 'base' ] );
		return $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
	}

	/**
	 * Check if the theme supports the setting and check user capabilities.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the setting or user can't change setting, otherwise true.
	 */
	public final function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
			return false;

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) )
			return false;

		return true;
	}

	/**
	 * Multidimensional helper function.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param bool $create Default is false.
	 * @return null|array
	 */
	final protected function multidimensional( &$root, $keys, $create = false ) {
		if ( $create && empty( $root ) )
			$root = array();

		if ( ! isset( $root ) || empty( $keys ) )
			return;

		$last = array_pop( $keys );
		$node = &$root;

		foreach ( $keys as $key ) {
			if ( $create && ! isset( $node[ $key ] ) )
				$node[ $key ] = array();

			if ( ! is_array( $node ) || ! isset( $node[ $key ] ) )
				return;

			$node = &$node[ $key ];
		}

		if ( $create && ! isset( $node[ $last ] ) )
			$node[ $last ] = array();

		if ( ! isset( $node[ $last ] ) )
			return;

		return array(
			'root' => &$root,
			'node' => &$node,
			'key'  => $last,
		);
	}

	/**
	 * Will attempt to replace a specific value in a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param mixed $value The value to update.
	 * @return
	 */
	final protected function multidimensional_replace( $root, $keys, $value ) {
		if ( ! isset( $value ) )
			return $root;
		elseif ( empty( $keys ) ) // If there are no keys, we're replacing the root.
			return $value;

		$result = $this->multidimensional( $root, $keys, true );

		if ( isset( $result ) )
			$result['node'][ $result['key'] ] = $value;

		return $root;
	}

	/**
	 * Will attempt to fetch a specific value from a multidimensional array.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @param $default A default value which is used as a fallback. Default is null.
	 * @return mixed The requested value or the default value.
	 */
	final protected function multidimensional_get( $root, $keys, $default = null ) {
		if ( empty( $keys ) ) // If there are no keys, test the root.
			return isset( $root ) ? $root : $default;

		$result = $this->multidimensional( $root, $keys );
		return isset( $result ) ? $result['node'][ $result['key'] ] : $default;
	}

	/**
	 * Will attempt to check if a specific value in a multidimensional array is set.
	 *
	 * @since 3.4.0
	 *
	 * @param $root
	 * @param $keys
	 * @return bool True if value is set, false if not.
	 */
	final protected function multidimensional_isset( $root, $keys ) {
		$result = $this->multidimensional_get( $root, $keys );
		return isset( $result );
	}
}