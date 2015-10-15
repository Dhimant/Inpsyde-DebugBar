/*global Backbone, jQuery, _ */

/*!
 * DebugBar
 */
(function( $ ) {
	"use strict";

	/** @namespace DebugBar */
	var DebugBar = Backbone.View.extend( {

		/**
		 * Holds the root element-name of the View
		 * @var string
		 */
		el: '.inpsyde-debugbar',

		/**
		 * contains all events of this view
		 * @var object
		 */
		events: {
			"click .inpsyde-debugbar__header"         : "toggleDebugBarVisibility",
			"click .inpsyde-debugbar__tab-item-link"  : "toggleTabs",
			"click .inpsyde-debugbar-log__detail-link": "toggleLogDetail"
		},

		initialize     : function() {},

		/**
		 * Callback on Click-Event to toggle the visibility of the Log-Details.
		 * @param event
		 */
		toggleLogDetail: function( event ) {
			event.preventDefault();
			var $this = $( event.currentTarget ),
				$parent = $this.parents( '.inpsyde-debugbar-log' );
			$parent.toggleClass( 'inpsyde-debugbar-log--is-open' );
		},

		/**
		 * Callback on Click-Event to toggle the visibility of the DebugBar.
		 * @param event
		 */
		toggleDebugBarVisibility: function( event ) {
			event.preventDefault();
			$( '.inpsyde-debugbar' ).toggleClass( 'inpsyde-debugbar--is-hidden' );
		},

		/**
		 * Callback on Click-Event to toggle Tabs.
		 * @param event
		 */
		toggleTabs: function( event ) {
			event.preventDefault();
			var $this = $( event.currentTarget ),
				target = $this.data( 'target' ),
				$tabContent = $( '#' + target ),
				$tabItem = $this.parent();

			$( '.inpsyde-debugbar__tab-item--is-selected' ).removeClass( 'inpsyde-debugbar__tab-item--is-selected' );
			$tabItem.addClass( 'inpsyde-debugbar__tab-item--is-selected' );

			$( '.inpsyde-debugbar__content' ).addClass( 'inpsyde-debugbar__content--is-hidden' );
			$tabContent.removeClass( 'inpsyde-debugbar__content--is-hidden' );
		}
	} );

	$( document ).ready( function() {
		new DebugBar();
	} );

})( jQuery, {} );