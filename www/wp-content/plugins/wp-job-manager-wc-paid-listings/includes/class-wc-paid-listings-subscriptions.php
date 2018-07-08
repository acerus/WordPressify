<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Paid_Listings_Subscriptions
 */
class WC_Paid_Listings_Subscriptions {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( class_exists( 'WC_Subscriptions_Synchroniser' ) && method_exists( 'WC_Subscriptions_Synchroniser', 'save_subscription_meta' ) ) {
			add_action( 'woocommerce_process_product_meta_job_package_subscription', 'WC_Subscriptions_Synchroniser::save_subscription_meta', 10 );
			add_action( 'woocommerce_process_product_meta_resume_package_subscription', 'WC_Subscriptions_Synchroniser::save_subscription_meta', 10 );
		}
		add_action( 'added_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_filter( 'woocommerce_is_subscription', array( $this, 'woocommerce_is_subscription' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'wp_trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );
		add_action( 'publish_to_expired', array( $this, 'check_expired_listing' ) );
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'filter_job_manager_job_listing_data_fields' ), 10, 2 );

		// Subscription is paused
		add_action( 'woocommerce_subscription_status_on-hold', array( $this, 'subscription_paused' ) ); // When a subscription is put on hold

		// Subscription is ended
		add_action( 'woocommerce_subscription_status_expired', array( $this, 'subscription_ended' ) ); // When a subscription expires
		add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'subscription_ended' ) ); // When the subscription status changes to cancelled

		// Subscription starts
		add_action( 'woocommerce_subscription_status_active', array( $this, 'subscription_activated' ) ); // When the subscription status changes to active

		// On renewal
		add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'subscription_renewed' ) ); // When the subscription is renewed

		// Subscription is switched
		add_action( 'woocommerce_subscriptions_switched_item', array( $this, 'subscription_switched' ), 10, 3 ); // When the subscription is switched and a new subscription is created
		add_action( 'woocommerce_subscription_item_switched', array( $this, 'subscription_item_switched' ), 10, 4 ); // When the subscription is switched and only the item is changed
	}

	/**
	 * Prevent listings linked to subscriptions from expiring.
	 *
	 * @param int         $meta_id
	 * @param int|WP_Post $object_id
	 * @param string      $meta_key
	 * @param mixed       $meta_value
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( false !== $this->get_listing_subscription_order_id( $object_id )
		     && '' !== $meta_value
		     && '_job_expires' === $meta_key
		) {
			update_post_meta( $object_id, '_job_expires', '' ); // Never expire automatically
		}
	}

	/**
	 * Filters the placeholder text for the job expires field and clears it if attached to
	 *
	 * @param array $fields
	 * @param int   $job_id
	 * @return array
	 */
	public function filter_job_manager_job_listing_data_fields( $fields, $job_id = false ) {
		if ( empty( $job_id) ) {
			return $fields;
		}
		$subscription_order_id = $this->get_listing_subscription_order_id( $job_id );
		if ( isset( $fields['_job_expires'] ) && false !== $subscription_order_id ) {
			$fields['_job_expires']['type'] = 'hidden';
			$fields['_job_expires']['placeholder'] = __( 'Inherited from subscription', 'wp-job-manager-wc-paid-listings' );
			$fields['_job_expires']['information'] = sprintf( __( 'Job listing expires with its <a href="%s">associated subscription</a>.', 'wp-job-manager-wc-paid-listings' ), admin_url( 'post.php?post=' . absint( $subscription_order_id ) . '&action=edit' ) );

			if ( WC_Paid_Listings::is_wpjm_pre( '1.26.3' ) ) {
				$fields['_job_expires']['type'] = 'text';
			}
		}
		return $fields;
	}

	/**
	 * If the job listing is tied to a subscription of type 'listing', return the order ID.
	 *
	 * @param int $job_id
	 *
	 * @return bool|int False if not found or is not the correct subscription type.
	 */
	private function get_listing_subscription_order_id( $job_id ) {
		if ( 'job_listing' === get_post_type( $job_id ) ) {
			$user_package_id = get_post_meta( $job_id, '_user_package_id', true );
			$user_package    = wc_paid_listings_get_user_package( $user_package_id );
			$package_id      = get_post_meta( $job_id, '_package_id', true );
			$package         = wc_get_product( $package_id );
			if ( $user_package
			     && $user_package->has_package()
				 && ( $package instanceof WC_Product_Job_Package_Subscription
			          || $package instanceof WC_Product_Resume_Package_Subscription )
			     && 'listing' === $package->get_package_subscription_type()
			) {
				return $user_package->get_order_id();
			}
		}
		return false;
	}

	/**
	 * get subscription type for package by ID
	 *
	 * @param  int $product_id
	 * @return string
	 */
	public function get_package_subscription_type( $product_id ) {
		$subscription_type = get_post_meta( $product_id, '_package_subscription_type', true );
		return empty( $subscription_type ) ? 'package' : $subscription_type;
	}

	/**
	 * Is this a subscription product?
	 *
	 * @param bool $is_subscription
	 * @param int  $product_id
	 * @return bool
	 */
	public function woocommerce_is_subscription( $is_subscription, $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) ) {
			$is_subscription = true;
		}
		return $is_subscription;
	}

	/**
	 * If a listing is expired, the pack may need it's listing count changing
	 *
	 * @param WP_Post $post
	 */
	public function check_expired_listing( $post ) {
		global $wpdb;

		if ( 'job_listing' === $post->post_type || 'resume' === $post->post_type ) {
			$package_product_id = get_post_meta( $post->ID, '_package_id', true );
			$package_id         = get_post_meta( $post->ID, '_user_package_id', true );
			$package_product    = get_post( $package_product_id );

			if ( $package_product_id ) {
				$subscription_type = $this->get_package_subscription_type( $package_product_id );

				if ( 'listing' === $subscription_type ) {
					$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
					$new_count --;

					$wpdb->update(
						"{$wpdb->prefix}wcpl_user_packages",
						array(
							'package_count'  => max( 0, $new_count ),
						),
						array(
							'id' => $package_id,
						)
					);

					// Remove package meta after adjustment
					delete_post_meta( $post->ID, '_package_id' );
					delete_post_meta( $post->ID, '_user_package_id' );
				}
			}
		}
	}

	/**
	 * If a listing gets trashed/deleted, the pack may need it's listing count changing
	 *
	 * @param int $id
	 */
	public function wp_trash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = $this->get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count --;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => max( 0, $new_count ),
							),
							array(
								'id' => $package_id,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * If a listing gets restored, the pack may need it's listing count changing
	 *
	 * @param int $id
	 */
	public function untrash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = $this->get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$package  = $wpdb->get_row( $wpdb->prepare( "SELECT package_count, package_limit FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count = $package->package_count + 1;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => min( $package->package_limit, $new_count ),
							),
							array(
								'id' => $package_id,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Subscription is on-hold for payment. Suspend package and listings.
	 *
	 * @param WC_Subscription $subscription
	 */
	public function subscription_paused( $subscription ) {
		$this->subscription_ended( $subscription, true );
	}

	/**
	 * Subscription has expired - cancel job packs
	 *
	 * @param WC_Subscription $subscription
	 * @param bool            $paused
	 */
	public function subscription_ended( $subscription, $paused = false ) {
		global $wpdb;

		foreach ( $subscription->get_items() as $item ) {
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );
			/**
			 * @var WC_Order $parent
			 */
			$parent            = $subscription->get_parent();
			$parent_id         = ! empty( $parent ) ? wc_paid_listings_get_order_id( $parent ) : null;
			$legacy_id         = isset( $parent_id ) ? $parent_id : wc_paid_listings_get_order_id( $subscription );
			$user_package      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", wc_paid_listings_get_order_id( $subscription ), $legacy_id, $item['product_id'] ) );

			if ( $user_package ) {
				// Delete the package
				$wpdb->delete(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'id' => $user_package->id,
					)
				);

				// Expire listings posted with package
				if ( 'listing' === $subscription_type ) {
					$listing_ids = wc_paid_listings_get_listings_for_package( $user_package->id );

					foreach ( $listing_ids as $listing_id ) {
						if ( $paused ) {
							// Record the current post status in case subscription is resumed
							update_post_meta( $listing_id, '_post_status_before_package_pause', get_post_status( $listing_id ) );
						} else {
							delete_post_meta( $listing_id, '_post_status_before_package_pause' );
						}
						$listing = array(
							'ID' => $listing_id,
							'post_status' => 'expired',
						);
						wp_update_post( $listing );

						// Make a record of the subscription ID in case of re-activation
						update_post_meta( $listing_id, '_expired_subscription_id', wc_paid_listings_get_order_id( $subscription ) );
					}
				}
			}
		}// End foreach().

		delete_post_meta( wc_paid_listings_get_order_id( $subscription ), 'wc_paid_listings_subscription_packages_processed' );
	}

	/**
	 * Subscription activated
	 *
	 * @param WC_Subscription $subscription
	 */
	public function subscription_activated( $subscription ) {
		global $wpdb;

		if ( get_post_meta( wc_paid_listings_get_order_id( $subscription ), 'wc_paid_listings_subscription_packages_processed', true ) ) {
			return;
		}

		// Remove any old packages for this subscription
		$parent            = $subscription->get_parent();
		$parent_id         = ! empty( $parent ) ? wc_paid_listings_get_order_id( $parent ) : null;
		$legacy_id         = isset( $parent_id ) ? $parent_id : wc_paid_listings_get_order_id( $subscription );
		$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array(
			'order_id' => $legacy_id,
		) );
		$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array(
			'order_id' => wc_paid_listings_get_order_id( $subscription ),
		) );

		foreach ( $subscription->get_items() as $item ) {
			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );

			// Give user packages for this subscription
			if ( $product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) && $subscription->get_user_id() && ! isset( $item['switched_subscription_item_id'] ) ) {

				// Give packages to user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = wc_paid_listings_give_user_package( $subscription->get_user_id(), $product->get_id(), wc_paid_listings_get_order_id( $subscription ) );
				}

				/**
				 * If the subscription is associated with listings, see if any
				 * already match this ID and approve them (useful on
				 * re-activation of a sub).
				 */
				if ( 'listing' === $subscription_type ) {
					$listing_ids = (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s", '_expired_subscription_id', wc_paid_listings_get_order_id( $subscription ) ) );
				} else {
					$listing_ids = array();
				}

				$listing_ids[] = isset( $item['job_id'] ) ? $item['job_id'] : '';
				$listing_ids[] = isset( $item['resume_id'] ) ? $item['resume_id'] : '';
				$listing_ids   = array_unique( array_filter( array_map( 'absint', $listing_ids ) ) );

				foreach ( $listing_ids as $listing_id ) {
					if ( in_array( get_post_status( $listing_id ), array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_listing_with_package( $listing_id, $subscription->get_user_id(), $user_package_id );
						delete_post_meta( $listing_id, '_expired_subscription_id' );
					}
				}
			}
		}

		update_post_meta( wc_paid_listings_get_order_id( $subscription ), 'wc_paid_listings_subscription_packages_processed', true );
	}

	/**
	 * Subscription renewed - renew the job pack
	 *
	 * @param WC_Subscription $subscription
	 */
	public function subscription_renewed( $subscription ) {
		global $wpdb;

		foreach ( $subscription->get_items() as $item ) {
			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );
			$parent            = $subscription->get_parent();
			$parent_id         = ! empty( $parent ) ? wc_paid_listings_get_order_id( $parent ) : null;
			$legacy_id         = isset( $parent_id ) ? $parent_id : wc_paid_listings_get_order_id( $subscription );

			// Renew packages which refresh every term
			if ( 'package' === $subscription_type ) {
				if ( ! $wpdb->update(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'package_count'  => 0,
					),
					array(
						'order_id'   => wc_paid_listings_get_order_id( $subscription ),
						'product_id' => $item['product_id'],
					)
				) ) {
					wc_paid_listings_give_user_package( $subscription->get_user_id(), $item['product_id'], wc_paid_listings_get_order_id( $subscription ) );
				}
			} else {
				// Otherwise the listings stay active, but we can ensure they are synced in terms of featured status etc
				if ( $user_package_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", wc_paid_listings_get_order_id( $subscription ), $legacy_id, $item['product_id'] ) ) ) {
					foreach ( $user_package_ids as $user_package_id ) {
						$package = wc_paid_listings_get_user_package( $user_package_id );

						if ( $listing_ids = wc_paid_listings_get_listings_for_package( $user_package_id ) ) {
							foreach ( $listing_ids as $listing_id ) {
								// Featured or not
								update_post_meta( $listing_id, '_featured', $package->is_featured() ? 1 : 0 );
							}
						}
					}
				}
			}
		}// End foreach().
	}

	/**
	 * When switching a subscription we need to update old listings.
	 *
	 * No need to give the user a new package; that is still handled by the orders class.
	 *
	 * @param WC_Order        $order
	 * @param WC_Subscription $subscription
	 * @param int             $new_order_item_id
	 * @param int             $old_order_item_id
	 */
	public function subscription_item_switched( $order, $subscription, $new_order_item_id, $old_order_item_id ) {
		global $wpdb;

		$new_order_item = WC_Subscriptions_Order::get_item_by_id( $new_order_item_id );
		$old_order_item = WC_Subscriptions_Order::get_item_by_id( $old_order_item_id );

		$new_subscription = (object) array(
			'id'           => wc_paid_listings_get_order_id( $subscription ),
			'subscription' => $subscription,
			'product_id'   => $new_order_item['product_id'],
			'product'      => wc_get_product( $new_order_item['product_id'] ),
			'type'         => $this->get_package_subscription_type( $new_order_item['product_id'] ),
		);

		$old_subscription = (object) array(
			'id'           => wc_paid_listings_get_order_id( $subscription ),
			'subscription' => $subscription,
			'product_id'   => $old_order_item['product_id'],
			'product'      => wc_get_product( $old_order_item['product_id'] ),
			'type'         => $this->get_package_subscription_type( $old_order_item['product_id'] ),
		);

		$this->switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	/**
	 * When switching a subscription we need to update old listings.
	 *
	 * No need to give the user a new package; that is still handled by the orders class.
	 *
	 * @param WC_Subscription $subscription
	 * @param array           $new_order_item
	 * @param array           $old_order_item
	 */
	public function subscription_switched( $subscription, $new_order_item, $old_order_item ) {
		global $wpdb;

		$new_subscription = (object) array(
			'id'         => wc_paid_listings_get_order_id( $subscription ),
			'product_id' => $new_order_item['product_id'],
			'product'    => wc_get_product( $new_order_item['product_id'] ),
			'type'       => $this->get_package_subscription_type( $new_order_item['product_id'] ),
		);

		$old_subscription = (object) array(
			'id'         => $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d ", $new_order_item['switched_subscription_item_id'] ) ),
			'product_id' => $old_order_item['product_id'],
			'product'    => wc_get_product( $old_order_item['product_id'] ),
			'type'       => $this->get_package_subscription_type( $old_order_item['product_id'] ),
		);

		$this->switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	/**
	 * Handle Switch Event
	 *
	 * @param int      $user_id
	 * @param stdClass $new_subscription
	 * @param stdClass $old_subscription
	 */
	public function switch_package( $user_id, $new_subscription, $old_subscription ) {
		global $wpdb;

		// Get the user package
		/**
		 * @var null|WC_Subscription $parent
		 */
		$parent            = isset( $old_subscription->subscription ) ? $old_subscription->subscription->get_parent() : null;
		$parent_id         = ! empty( $parent ) ? wc_paid_listings_get_order_id( $parent ) : null;
		$legacy_id         = isset( $parent_id ) ? $parent_id : $old_subscription->id;
		$user_package      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", $old_subscription->id, $legacy_id, $old_subscription->product_id ) );

		if ( $user_package ) {
			// If invalid, abort
			if ( ! $new_subscription->product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) ) {
				return false;
			}

			// Give new package to user
			$switching_to_package_id = wc_paid_listings_give_user_package( $user_id, $new_subscription->product_id, $new_subscription->id );

			// Upgrade?
			$is_upgrade = ( 0 === $new_subscription->product->get_limit() || $new_subscription->product->get_limit() >= $user_package->package_count );

			// Delete the old package
			$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array(
				'id' => $user_package->id,
			) );

			$does_new_subscription_feature_listings = false;
			if ( $new_subscription->product instanceof WC_Product_Job_Package_Subscription ) {
				$does_new_subscription_feature_listings = $new_subscription->product->is_job_listing_featured();
			} elseif ( $new_subscription->product instanceof WC_Product_Resume_Package_Subscription ) {
				$does_new_subscription_feature_listings = $new_subscription->product->is_resume_featured();
			}

			// Update old listings
			if ( 'listing' === $new_subscription->type && $switching_to_package_id ) {
				$listing_ids = wc_paid_listings_get_listings_for_package( $user_package->id );

				foreach ( $listing_ids as $listing_id ) {
					// If we are not upgrading, expire the old listing
					if ( ! $is_upgrade ) {
						$listing = array(
							'ID' => $listing_id,
							'post_status' => 'expired',
						);
						wp_update_post( $listing );
					} else {
						/** This filter is documented in includes/package-functions.php */
						if ( apply_filters( 'job_manager_job_listing_affects_package_count', true, $listing_id ) ) {
							wc_paid_listings_increase_package_count( $user_id, $switching_to_package_id );
						}
						// Change the user package ID and package ID
						update_post_meta( $listing_id, '_user_package_id', $switching_to_package_id );
						update_post_meta( $listing_id, '_package_id', $new_subscription->product_id );
					}

					// Featured or not
					update_post_meta( $listing_id, '_featured', $does_new_subscription_feature_listings ? 1 : 0 );

					// Fire action
					do_action( 'wc_paid_listings_switched_subscription', $listing_id, $user_package );
				}
			}
		}// End if().
	}
}
WC_Paid_Listings_Subscriptions::get_instance();
