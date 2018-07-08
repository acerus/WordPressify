<?php
namespace Layered\IfMenu;

class Admin {

	public static function start() {
		return new static;
	}

	protected function __construct() {
		add_action('admin_init', [$this, 'actions']);
		add_action('admin_menu', [$this, 'menu']);
		add_action('admin_enqueue_scripts', [$this, 'assets']);
	}

	public function actions() {
		if (isset($_POST['if-menu-settings'])) {
			update_option('if-menu-peak', isset($_POST['if-menu-peek']) && $_POST['if-menu-peek'] == 1 ? 1 : 0);
			update_option('if-menu-admin', isset($_POST['if-menu-admin']) && $_POST['if-menu-admin'] == 1 ? 1 : 0);
		}
	}

	public function assets() {
		global $pagenow;

		if ($pagenow == 'themes.php') {
			wp_enqueue_style('if-menu', plugins_url('assets/if-menu.css', dirname(__FILE__)), '0.9');
		}
	}

	public function menu() {
		add_submenu_page('themes.php', 'If Menu', 'If Menu', 'edit_theme_options', 'if-menu', [$this, 'page']);
	}

	public function page() {
		$ifMenuPeek = get_option('if-menu-peak');
		$ifMenuAdmin = get_option('if-menu-admin', 1);
		$plan = \If_Menu::getPlan();
		?>

		<div class="wrap about-wrap if-menu-wrap">
			<h1>If Menu</h1>
			<p class="about-text"><?php _e('Thanks for using <strong>If Menu</strong>! Now you can display tailored menu items to each visitor, based on visibility rules. Here are a few examples:', 'if-menu') ?></p>
			<ul class="list">
				<li><?php _e('Hide Login or Register links for logged-in users:', 'if-menu') ?> <code><span class="if-menu-red"><?php _e('Hide', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User is logged in', 'if-menu') ?></span></code></li>
				<li><?php _e('Display Logout link for logged-in users:', 'if-menu') ?> <code><span class="if-menu-green"><?php _e('Show', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User is logged in', 'if-menu') ?></span></code></li>
				<li><?php _e('Hide menu item on mobile devices:', 'if-menu') ?> <code><span class="if-menu-red"><?php _e('Hide', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('Mobile', 'if-menu') ?></span></code></li>
				<li><?php _e('Display menu item for users in US and UK:', 'if-menu') ?> <code><span class="if-menu-green"><?php _e('Show', 'if-menu') ?></span> <?php _e('if', 'if-menu') ?> <span class="if-menu-purple"><?php _e('User from country: US, UK', 'if-menu') ?></span></code></li>
			</ul>
			<hr class="wp-header-end">

			<div class="feature-section pricing-plan-section two-col">
				<div class="col">
					<div class="pricing-cell <?php if (!$plan || $plan['plan'] == 'free') echo 'selected' ?>">
						<span class="price"><small><?php _e('Free', 'if-menu') ?></small></span>
						<h3><?php _e('Basic', 'if-menu') ?></h3>
						
						<ul>
							<li>
								<?php _e('Basic visibility rules:', 'if-menu') ?>
								<ul>
									<li><?php _e('User role - is Admin, Editor, Author or Shop Manager', 'if-menu') ?></li>
									<li><?php _e('User state - visitor is logged in or out', 'if-menu') ?></li>
									<li><?php _e('Visitor device - detect mobile or desktop', 'if-menu') ?></li>
								</ul>
							</li>
							<li><?php _e('Support on WordPress forum', 'if-menu') ?></li>
						</ul>
						
						<p>
							<?php if (!$plan || $plan['plan'] == 'free') : ?>
								<button class="button disabled"><?php _e('Current plan', 'if-menu') ?></button>
							<?php endif ?>
						</p>
					</div>
				</div>

				<div class="col">
					<div class="pricing-cell <?php if ($plan && $plan['plan'] == 'premium') echo 'selected' ?>">
						<span class="price">$15<small>/<?php _e('annually', 'if-menu') ?></small></span>
						<h3><?php _e('Premium', 'if-menu') ?></h3>

						<ul>
							<li>
								<?php _e('Advanced visibility rules:', 'if-menu') ?>
								<ul>
									<li><?php _e('Visitor location - detect visitor\'s country', 'if-menu') ?></li>
								</ul>
							</li>
							<li>
								<?php _e('3rd-party plugin integrations:', 'if-menu') ?>
								<ul>
									<li><a href="https://woocommerce.com/products/woocommerce-subscriptions" target="_blank">WooCommerce Subscriptions</a> - <?php _e('Customer has active subscription', 'if-menu') ?></li>
									<li><a href="https://woocommerce.com/products/woocommerce-memberships" target="_blank">WooCommerce Memberships</a> - <?php _e('Customer has active membership plan', 'if-menu') ?></li>
									<li><a href="https://wordpress.org/plugins/groups" target="_blank">Groups</a> - <?php _e('Users are in a Group', 'if-menu') ?></li>
									<li><a href="https://member.wishlistproducts.com" target="_blank">WishList Member</a> - <?php _e('Users above a Membership Level', 'if-menu') ?></li>
									<li><a href="https://astoundify.com/products/wp-job-manager-listing-payments/" target="_blank">Listing Payments</a> - <?php _e('Customer has active Job Manager Listing subscription', 'if-menu') ?></li>
									<li><a href="https://restrictcontentpro.com/" target="_blank">Restrict Content Pro</a> - <?php _e('User has Subscription Level', 'if-menu') ?></li>
								</ul>
							</li>
							<li><?php _e('Priority support', 'if-menu') ?></li>
						</ul>

						<p class="description">
							<?php if ($plan && $plan['plan'] == 'premium') : ?>
								<button class="button disabled"><?php _e('Current plan', 'if-menu') ?></button>
								<br><br><small class="text-muted"><?php printf(__('Active until %s', 'if-menu'), date(get_option('date_format'), strtotime($plan['until']))) ?></small>
							<?php else : ?>
								<a href="https://wordpress.layered.studio/get-premium?site=<?php echo urlencode(site_url()) ?>&_nonce=<?php echo \If_Menu::apiNonce('get-premium') ?>" class="button button-primary"><?php _e('Get premium', 'if-menu') ?></a>
							<?php endif ?>
						</p>
					</div>
				</div>
			</div>

			<hr>

			<h3 class="title"><?php _e('Settings', 'if-menu') ?></h3>

			<form method="post" action="">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('If Menu peek', 'if-menu') ?></th>
							<td>
								<fieldset>
									<label><input type="checkbox" name="if-menu-peek" value="1" <?php checked($ifMenuPeek, 1) ?>> <?php _e('If Menu peek', 'if-menu') ?></label><br>
								</fieldset>
								<p class="description"><?php _e('Let administrators preview hidden menu items on website (useful for testing)', 'if-menu') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Enabled in Admin panel', 'if-menu') ?></th>
							<td>
								<fieldset>
									<label><input type="checkbox" name="if-menu-admin" value="1" <?php checked($ifMenuAdmin, 1) ?>> <?php _e('Filter menu items in Admin panel', 'if-menu') ?></label><br>
								</fieldset>
								<p class="description"><?php _e('If disabled, all menu items will be displayed in Admin panel, regardless of any visibility rules', 'if-menu') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<p class="submit"><input type="submit" name="if-menu-settings" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'if-menu') ?>"></p>
							</td>
						</tr>
					</tbody>
				</table>
			</form>

			<hr>

			<p class="text-right">
				<strong>If Menu</strong>:
				<a href="https://wordpress.org/plugins/if-menu/#faq" target="wpplugins"><?php _e('FAQs', 'if-menu') ?></a> &middot;
				<a href="https://wordpress.org/plugins/if-menu/#reviews" target="wpplugins"><?php _e('Reviews', 'if-menu') ?></a> &middot;
				<a href="https://wordpress.org/support/plugin/if-menu" target="wpplugins"><?php _e('Support', 'if-menu') ?></a>
			</p>
		</div>

		<?php
	}

}
