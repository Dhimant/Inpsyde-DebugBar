<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 15.10.2015
 * Time: 10:45
 */

namespace Inpsyde\DebugBar\Model;

class Config {

	/**
	 * List of properties.
	 *
	 * @type array
	 */
	protected $properties = array();

	/**
	 * Parent object.
	 *
	 * Used if a name is not available in this instance.
	 *
	 * @type Config
	 */
	protected $parent = NULL;

	/**
	 * Record of deleted properties.
	 *
	 * Prevents access to the parent object's properties after deletion
	 * in this instance.
	 *
	 * @see  get()
	 * @type array
	 */
	protected $deleted = array();

	/**
	 * Write and delete protection.
	 *
	 * @see  freeze()
	 * @see  is_frozen()
	 * @type bool
	 */
	protected $frozen = FALSE;

	/**
	 * Set new value.
	 *
	 * @param  string $name
	 * @param  mixed $value
	 *
	 * @return void|Config
	 */
	public function set( $name, $value ) {
		if ( $this->frozen ) {
			return $this->stop(
				'This object has been frozen.
				You cannot set properties anymore.'
			);
		}

		$this->properties[ $name ] = $value;
		unset ( $this->deleted[ $name ] );

		return $this;
	}

	/**
	 * Import an array or an object as properties.
	 *
	 * @param  array|object $var
	 *
	 * @return void|Config
	 */
	public function import( $var ) {
		if ( $this->frozen ) {
			return $this->stop(
				'This object has been frozen.
				You cannot set properties anymore.'
			);
		}

		if ( ! is_array( $var ) and ! is_object( $var ) ) {
			return $this->stop(
				'Cannot import this variable.
				Use arrays and objects only, not a "' . gettype( $var ) . '".'
			);
		}

		foreach ( $var as $name => $value ) {
			$this->properties[ $name ] = $value;
		}

		return $this;
	}

	/**
	 * Get a value.
	 *
	 * Might be taken from parent object.
	 *
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public function get( $name ) {

		if ( isset ( $this->properties[ $name ] ) ) {
			return $this->properties[ $name ];
		}

		if ( isset ( $this->deleted[ $name ] ) ) {
			return NULL;
		}

		if ( NULL === $this->parent ) {
			return NULL;
		}

		return $this->parent->get( $name );
	}

	/**
	 * Get all properties.
	 *
	 * @param  boolean $use_parent Get parent object's properties too.
	 *
	 * @return array
	 */
	public function get_all( $use_parent = FALSE ) {
		if ( ! $use_parent ) {
			return $this->properties;
		}

		$parent_properties = $this->parent->get_all( TRUE );
		$all               = array_merge( $parent_properties, $this->properties );

		// Strip out properties existing in the parent but deleted in this
		// instance.
		return array_diff( $all, $this->deleted );
	}

	/**
	 * Check if property exists.
	 *
	 * Due to syntax restrictions in PHP we cannot name this "isset()".
	 *
	 * @param  string $name
	 *
	 * @return boolean
	 */
	public function has( $name ) {

		if ( isset ( $this->properties[ $name ] ) ) {
			return TRUE;
		}

		if ( isset ( $this->deleted[ $name ] ) ) {
			return FALSE;
		}

		if ( NULL === $this->parent ) {
			return FALSE;
		}

		return $this->parent->has( $name );
	}

	/**
	 * Delete a key and set its name to the $deleted list.
	 *
	 * Further calls to has() and get() will not take this property into account.
	 *
	 * @param  string $name
	 *
	 * @return void|Config
	 */
	public function delete( $name ) {

		if ( $this->frozen ) {
			return $this->stop(
				'This object has been frozen.
				You cannot delete properties anymore.'
			);
		}

		$this->deleted[ $name ] = TRUE;
		unset ( $this->properties[ $name ] );

		return $this;
	}

	/**
	 * Set parent object. Properties of this object will be inherited.
	 *
	 * @param  Config $object
	 *
	 * @return Config
	 */
	public function set_parent( Config $object ) {

		if ( $this->frozen ) {
			return $this->stop(
				'This object has been frozen.
				You cannot change the parent anymore.'
			);
		}

		$this->parent = $object;

		return $this;
	}

	/**
	 * Test if the current instance has a parent.
	 *
	 * @return boolean
	 */
	public function has_parent() {

		return NULL === $this->parent;
	}

	/**
	 * Lock write access to this object's instance. Forever.
	 *
	 * @return Config
	 */
	public function freeze() {

		$this->frozen = TRUE;

		return $this;
	}

	/**
	 * Test from outside if an object has been frozen.
	 *
	 * @return boolean
	 */
	public function is_frozen() {

		return $this->frozen;
	}

	/**
	 * Used for attempts to write to a frozen instance.
	 *
	 * Might be replaced by a child class.
	 *
	 * @param  string $msg  Error message. Always be specific.
	 * @param  string $code Re-use the same code to group error messages.
	 *
	 * @throws \Exception
	 *
	 * @return void|\WP_Error
	 */
	protected function stop( $msg, $code = '' ) {

		if ( '' === $code ) {
			$code = __CLASS__;
		}

		if ( class_exists( 'WP_Error' ) ) {
			return new \WP_Error( $code, $msg );
		}

		throw new \Exception( $msg, $code );
	}

	# ==== Magic functions ==== #

	/**
	 * Wrapper for set().
	 *
	 * @see    set()
	 *
	 * @param  string $name
	 * @param  mixed $value
	 *
	 * @return Config
	 */
	public function __set( $name, $value ) {

		return $this->set( $name, $value );
	}

	/**
	 * Wrapper for get()
	 *
	 * @see    get()
	 *
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {

		return $this->get( $name );
	}

	/**
	 * Wrapper for has().
	 *
	 * @see    has()
	 *
	 * @param  string $name
	 *
	 * @return boolean
	 */
	public function __isset( $name ) {

		return $this->has( $name );
	}

}