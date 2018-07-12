<?php
/**
 * Plugin Name: Suppress _doing_it_wrong errors.
 * Description: Will suppress certain _doing_it_wrong errors.
 * Version:     1.0.0
 * Author:      WebDevStudios
 *
 * Note you can use this as a normal class if you want, or drop this in
 * as a mu-plugin and use the filter `suppress_doing_it_wrong_strings` to add
 * your own strings below.
 *
 * @since       1.0.0
 * @package     wds-log-cleaner
 */

namespace WDS\Util;

/**
 * Suppress a _doing_it_wrong message.
 *
 * @since  1.0.0
 */
class Suppress_Doing_It_Wrong_Message {

	/**
	 * We toggle this to either suppress a message or not.
	 *
	 * @since 1.0.0
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 *
	 * @var boolean
	 */
	private $suppress = false;

	/**
	 * The string we want to search for in the message (set in construct).
	 *
	 * @since  1.0.0
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 *
	 * @var string
	 */
	private $string = '';

	/**
	 * Construct.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @param  string $string The string, e.g. `aubrey` will
	 *                        suppress any `_doing_it_wrong` message that contains it.
	 *                        Being more verbose is obviously better.
	 */
	public function __construct( $string ) {
		if ( is_string( $string ) ) {

			// Set the string.
			$this->string = $string;
		}

		// Fire hooks.
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  1.0.0
	 */
	private function hooks() {

		// First detect (for whatever doing it wrong error happens), if we should suppress it.
		add_action( 'doing_it_wrong_run', array( $this, 'check_message' ), 10, 3 );

		// Next when they actually trigger the error, if we did suppress the last message, don't let the if() pass.
		add_filter( 'doing_it_wrong_trigger_error', array( $this, 'maybe_suppress_trigger_error' ) );
	}

	/**
	 * Check if the message contains our string, and toggle suppression or not.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @param  string $function The function.
	 * @param  string $message  The message.
	 * @param  string $version  The version.
	 */
	public function check_message( $function, $message, $version ) {
		if ( stristr( $message, $this->string ) ) {
			$this->suppress = true;
		} else {
			$this->suppress = false;
		}
	}

	/**
	 * Actually suppress the _doing_it_wrong message.
	 *
	 * Depending on what the `doing_it_wrong_run` hook and what the `self::suppress`
	 * was set to there, change whether the trigger is actually shown or not.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  1.0.0
	 *
	 * @return boolean We pass true to suppress, false not.
	 */
	public function maybe_suppress_trigger_error() {
		if ( $this->suppress ) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * Create objects to suppress certain _doing_it_wrong messages.
 *
 * @author Aubrey Portwood <aubrey@webdevstudios.com>
 * @since  1.0.0
 */
function suppress_doing_it_wrong_message() {

	/**
	 * Filter strings that will help suppress _doing_it_wrong messages.
	 *
	 * For instance adding the `aubrey` string will suppress any _doing_it_wrong message
	 * that contains that string.
	 *
	 * @since  1.0.0
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 *
	 * @param array $strings The strings.
	 */
	$strings = apply_filters( 'suppress_doing_it_wrong_strings', array(
		'hyperdb', // WPE always shows these stupid hyperdb messages, just stop them!
		'7197566a25a6ade61e15dd52b7830c1c', // Suppress our test message as a test!
	) );

	$suppressed_doing_it_wrong_messages = array();
	foreach ( $strings as $string ) {
		$suppressed_doing_it_wrong_messages[ $string ] = new Suppress_Doing_It_Wrong_Message( $string );
	}

	// This _doing_it_wrong should get suppressed (self test).
	_doing_it_wrong( 'suppress_me_function', "7197566a25a6ade61e15dd52b7830c1c: If you are seeing me, this thing isn't working.", '0.0' );

	if ( defined( 'DEBUG_SUPPRESS_DOING_IT_WRONG_MESSAGE_PASS' ) && DEBUG_SUPPRESS_DOING_IT_WRONG_MESSAGE_PASS ) {

		// Show a _doing_it_wrong message we're definitely not going to suppress.
		_doing_it_wrong( 'dont_suppress_me_function', md5( (string) rand( 0, 225 ) ), '0.0' ); // @codingStandardsIgnoreLine
	}
}

// Start suppressing things.
suppress_doing_it_wrong_message();
