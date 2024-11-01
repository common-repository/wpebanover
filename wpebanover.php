<?php
/*
 Plugin Name: WPeBanOver
 Description: Show a small banner and on mouse event show another big or 2nd banner anywhere in your template, post, page or widget. For print place `if ((function_exists('WPeBanOver'))) WPeBanOver(); ` in your templates or use shortcode [WPeBanOver]
 Version: 1.1
 Author: Esteban Truelsegaard <esteban@netmdp.com>
 Author URI: http://www.netmdp.com
 */
# @charset utf-8

if ( ! function_exists( 'add_filter' ) )
	exit;

if ( ! class_exists( 'WPeBanOver' ) ) {

	add_action( 'init', array( 'WPeBanOver', 'init' ) );

	#register_aktivation_hook( plugin_basename( __FILE__ ), array( 'WPeBanOver', 'activate' ) );
	#register_deactivation_hook( plugin_basename( __FILE__ ), array( 'WPeBanOver', 'deactivate' ) );
	register_uninstall_hook( plugin_basename( __FILE__ ), array( 'WPeBanOver', 'uninstall' ) );

	function WPeBanOver_method() {
		//wp_deregister_script( 'BanOver' );
		//wp_register_style( 'banover',  plugin_dir_url( __FILE__ ).'banover.css');
        //wp_enqueue_style( 'banover' );
		wp_register_script( 'banover', plugin_dir_url( __FILE__ ).'banover.js');
		wp_enqueue_script( 'banover' );
	}    
	
	add_action('wp_enqueue_scripts', 'WPeBanOver_method');

	function get_WPeBanOver(){
		$campos = get_option( 'WPeBanOver_Options' );
		$bigbanner=stripslashes($campos['bigbanner']);
		$littlebanner=stripslashes($campos['littlebanner']);
		$over=$campos['over'];
		$overbig=$campos['overbig'];
		$fade=$campos['fade'];
		$marquesina = '';
		
		$marquesina .= '<div class="WPeBanOver">';
		$marquesina .= '<div id="littlebanner"><span ';
		switch ($over) {
		case 1:
			$marquesina .= 'onclick';
			break;
		case 2:
			$marquesina .= 'onmouseover';
			break;
		case 3:
			$marquesina .= 'ondblclick';
			break;
		default:
			$marquesina .= 'onmouseover';
		}
		if($fade) 
			$marquesina .= '="jQuery(\'#littlebanner\').fadeOut();jQuery(\'#bigbanner\').fadeIn();">' . $littlebanner . '</span></div>';
		else 
			$marquesina .= '="HideDIV(\'littlebanner\');DisplayDIV(\'bigbanner\')">' . $littlebanner . '</span></div>';
		$marquesina .= '<div id="bigbanner" style="display:none"><span ';
		switch ($overbig) {
		case 1:
			$marquesina .= 'onclick';
			break;
		case 2:
			$marquesina .= 'onmouseOut';
			break;
		case 3:
			$marquesina .= 'ondblclick';
			break;
		case 99:
			$marquesina .= 'nada';
			break;
		default:
			$marquesina .= 'onmouseover';
		}
		if($fade) 
			$marquesina .= '="jQuery(\'#bigbanner\').fadeOut();jQuery(\'#littlebanner\').fadeIn();">' . $bigbanner . '</span></div>';
		else 
			$marquesina .= '="HideDIV(\'bigbanner\');DisplayDIV(\'littlebanner\')">' . $bigbanner . '</span></div>';
		
		$marquesina .= '</div>';
		return $marquesina;
	}
	function WPeBanOver($atts = array(), $content = null){
		echo get_WPeBanOver();
	}
	
	add_filter( 'widget_text', 'shortcode_unautop');
	add_filter( 'widget_text', 'do_shortcode');
	add_shortcode("WPeBanOver", "WPeBanOver");
	
	class WPeBanOver {

		/**
		 * Textdomain
		 *
		 * @access public
		 * @const string
		 */
		const TEXTDOMAIN = 'wpebanover';

		/**
		 * Version
		 *
		 * @access public
		 * @const string
		 */
		const VERSION = '1.1';

		/**
		 * Option Key
		 *
		 * @access public
		 * @const string
		 */
		const OPTION_KEY = 'WPeBanOver_Options';

		/**
		 * $uri
		 *
		 * absolute uri to the plugin with trailing slash
		 *
		 * @access public
		 * @static
		 * @var string
		 */
		public static $uri = '';

		/**
		 * $dir
		 *
		 * filesystem path to the plugin with trailing slash
		 *
		 * @access public
		 * @static
		 * @var string
		 */
		public static $dir = '';

		/**
		 * $default_options
		 *
		 * Some settings to use by default
		 *
		 * @access protected
		 * @static
		 * @var array
		 */
		protected static $default_options = array(
			'littlebanner' => '<img src="/wp-content/plugins/wpebanover/demo-1.jpg">',
			'bigbanner' => '<a href="#"><img src="/wp-content/plugins/wpebanover/demo-2.jpg"></a>',			
			'over' => 0,			
			'overbig' => 2,			
			'fade' => false,			
		);

		/**
		 *
		 * $options
		 *
		 * @access protected
		 * @var array
		 */
		protected $options = array();

		/**
		 * init
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public function init() {

			self :: $uri = plugin_dir_url( __FILE__ );
			self :: $dir = plugin_dir_path( __FILE__ );
			self :: load_textdomain_file();
			new self( TRUE );
		}

		/**
		 * constructor
		 *
		 * @access public
		 * @param bool $hook_in
		 * @return void
		 */
		public function __construct( $hook_in = FALSE ) {

			$this->load_options();

			if ( $hook_in ) {
				add_action( 'admin_init', array( &$this, 'admin_init' ) );
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			}
		}
		public function admin_init() {
			wp_register_style( 'oplugincss', plugin_dir_url( __FILE__ ).'oplugins.css');
			wp_register_script( 'opluginjs', plugin_dir_url( __FILE__ ).'oplugins.js');
		}

		/**
		 * admin menu
		 *
		 * @access public
		 * @return void
		 */
		public function admin_menu() {
			$page= add_submenu_page(
				'options-general.php',
				__( 'WPeBanOver', self :: TEXTDOMAIN ),
				__( 'WPeBanOver', self :: TEXTDOMAIN ),
				'manage_options',
				'WPeBanOver',
				array( &$this, 'add_admin_submenu_page' )
			);
			add_action('admin_print_styles-' . $page,  array( &$this, 'WPeBanOver_adminfiles') );
		}
		
		public function WPeBanOver_adminfiles () {
			wp_enqueue_style( 'oplugincss' );
			wp_enqueue_script( 'opluginjs' );
		}

		/**
		 * an admin submenu page
		 *
		 * @access public
		 * @return void
		 */
		public function add_admin_submenu_page () {
			if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
				if ( get_magic_quotes_gpc() ) {
					$_POST = array_map( 'stripslashes_deep', $_POST );
				}

				# evaluation goes here
				$this->options = $_POST;

				# saving
				if ( $this->update_options() ) {
					?><div class="updated"><p> <?php _e( 'Settings saved', self :: TEXTDOMAIN );?></p></div><?php
				}
			}
			
			$this->load_options();
			?>
			<div class="wrap">
				<h2><?php _e( 'WPeBanOver settings', self :: TEXTDOMAIN );?></h2>
				<form method="post" action="">
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Donate', self :: TEXTDOMAIN );?></h3>
								<div class="inside">
									<p>WPeBanOver <?php echo self :: VERSION ; ?></p>
									<p><?php _e( 'Thanks for test, use and enjoy this plugin.', self :: TEXTDOMAIN );?></p>
									<p><?php _e( 'If you like it, I really appreciate a donation.', self :: TEXTDOMAIN );?></p>
									<p>
									<input type="button" class="button-primary" name="donate" value="<?php _e( 'Click for Donate', self :: TEXTDOMAIN );?>" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7267TH4PT3GSW');return false;"/>
									</p>
									<p><?php /*/ _e('Help', self :: TEXTDOMAIN ); ?><a href="#" onclick="javascript:window.open('https://www.paypal.com/ar/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img  src="https://www.paypal.com/es_XC/Marketing/i/logo/bnr_airlines1_205x67.gif" border="0" alt="Paypal Help"></a>  */ ?>
									</p>
									<p></p>
								</div>
							</div>
							<div class="postbox">
								<h3 class="handle"><?php _e( 'Knows my plugins', self :: TEXTDOMAIN );?></h3>
								<div class="inside" style="margin: 0 -12px -12px -10px;">
									<div class="wpeplugname" id="wpebanover"><a href="http://wordpress.org/extend/plugins/wpebanover/" target="_Blank" class="wpelinks">WPeBanOver</a>
									<div id="wpebanoverdesc" class="tsmall" style="display:none;">Show a small banner and on mouse event (over, out, click, dblclick) show another big or 2nd banner anywhere in your template, post, page or widget.</div></div>
									<p></p>
									<div class="wpeplugname" id="WPeMatico"><a href="http://wordpress.org/extend/plugins/wpematico/" target="_Blank" class="wpelinks">WPeMatico</a>
									<div id="WPeMaticodesc" class="tsmall" style="display:none;"> WPeMatico is for autoblogging. Drink a coffee meanwhile WPeMatico publish your posts. Post automatically from the RSS/Atom feeds organized into campaigns.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeDPC"><a href="http://wordpress.org/extend/plugins/etruel-del-post-copies/" target="_Blank" class="wpelinks">WP-eDel post copies</a>
									<div id="WPeDPCdesc" class="tsmall" style="display:none;">WPeDPC search for duplicated title name or content in posts in the categories that you selected and let you TRASH all duplicated posts in manual mode or automatic scheduled with WordPress Cron.</a></div></div>
									<p></p>
									<div class="wpeplugname" id="WPeBacklinks"><a href="http://www.netmdp.com/2011/10/wpebacklinks/" target="_Blank" class="wpelinks">WPeBacklinks</a>
									<div id="WPeBacklinksdesc" class="tsmall" style="display:none;">Backlinks.com’s original plugin allow only one key for wordpress site.
									This plugin makes it easier to use different keys to use Backlinks assigned for each page or section of wordpress. If you want to make some money, please register on <a href="http://www.backlinks.com/?aff=52126" class="wpeoverlink" target="_Blank">Backlinks.com here.</a></div></div>
									<p></p>
								</div>
							</div>
						</div>
					</div>
					<div id="post-body">
						<div id="post-body-content">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox inside">
									<h3><?php _e( 'WPeBanOver options', self :: TEXTDOMAIN );?></h3>
									<div class="inside">
									<?php // WPeBanOver(); ?>
										<a style="float:right;" target="_Blank" href="http://www.netmdp.com"><img src="<?php echo self :: $uri ; ?>NetMdP.png"></a>
										<p></p>
										<div><strong><?php _e( 'The code for the little banner.', self :: TEXTDOMAIN );?></strong><br />
											<div style="display: table;margin: 10px 0;">
											<div style="display: table-cell;padding-right: 10px;">
												<i><?php _e( 'Change on', self :: TEXTDOMAIN );?> </i>
											</div>
											<div>
											<div>
												<input type="radio" name="over" value="1"<?php checked( 1 == $this->options['over'] ); ?> /> <?php _e( 'Click', self :: TEXTDOMAIN );?>
											</div>
											<div>
												<input type="radio" name="over" value="0"<?php checked( 0 == $this->options['over'] ); ?> /> <?php _e( 'MouseOver', self :: TEXTDOMAIN );?> 
											</div>
											<div>
												<input type="radio" name="over" value="2"<?php checked( 2 == $this->options['over'] ); ?> /> <?php _e( 'MouseOut', self :: TEXTDOMAIN );?> 
											</div>
											<div>
												<input type="radio" name="over" value="3"<?php checked( 3 == $this->options['over'] ); ?> /> <?php _e( 'Double Click', self :: TEXTDOMAIN );?> 
											</div>
											</div>
											</div>
											<input name="fade" id="fade" class="checkbox" value="1" type="checkbox"<?php checked($this->options['fade'],true) ?> /> <?php _e( 'Change with Fade.', self :: TEXTDOMAIN );?>
											<br />
											<textarea id="flittlebanner" name="littlebanner" rows="8" cols="80"><?php echo stripslashes($this->options['littlebanner']); ?></textarea>
										</div>
										<p></p>
										
										<div>										
										<strong><?php _e( 'The code for the big or 2nd banner.', self :: TEXTDOMAIN );?></small></strong><br />
											<div style="display: table;margin: 10px 0;">
											<div style="display: table-cell;padding-right: 10px;">
												<i><?php _e( 'Change on', self :: TEXTDOMAIN );?> </i>
											</div>
											<div>
											<div>
												<input type="radio" name="overbig" value="1"<?php checked( 1 == $this->options['overbig'] ); ?> /> <?php _e( 'Click', self :: TEXTDOMAIN );?> 
											</div>
											<div>
												<input type="radio" name="overbig" value="0"<?php checked( 0 == $this->options['overbig'] ); ?> /> <?php _e( 'MouseOver', self :: TEXTDOMAIN );?>
											</div>
											<div>
												<input type="radio" name="overbig" value="2"<?php checked( 2 == $this->options['overbig'] ); ?> /> <?php _e( 'MouseOut', self :: TEXTDOMAIN );?>
											</div>
											<div>
												<input type="radio" name="overbig" value="3"<?php checked( 3 == $this->options['overbig'] ); ?> /> <?php _e( 'Double Click', self :: TEXTDOMAIN );?>
											</div>
											<div>
												<input type="radio" name="overbig" value="99"<?php checked( 99 == $this->options['overbig'] ); ?> /> <?php _e( 'Nothing', self :: TEXTDOMAIN );?> <?php _e( '(never change again until reload)', self :: TEXTDOMAIN );?>
											</div>
											</div>
											</div>
											<textarea id="fbigbanner" name="bigbanner" rows="8" cols="80"><?php echo stripslashes($this->options['bigbanner']); ?></textarea>
										</div>
										
										<p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save');?>" /></p>
										<p><strong><small><?php _e('After save you can see the preview below.', self :: TEXTDOMAIN );?></small></strong><br /></p>
										<script>function HideDIV(d) { document.getElementById(d).style.display = "none"; }
										function DisplayDIV(d) { document.getElementById(d).style.display = "block"; }</script>
										<?php do_shortcode("[WPeBanOver]"); ?>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				</form>

			</div><?php
		}
		/**
		 * load_textdomain_file
		 *
		 * @access protected
		 * @return void
		 */
		protected function load_textdomain_file() {
			# load plugin textdomain
			load_plugin_textdomain( self :: TEXTDOMAIN, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang' );			
			//load_plugin_textdomain( self :: TEXTDOMAIN, FALSE, basename( plugin_basename( __FILE__ ) ) . '/lang' );
			# load tinyMCE localisation file
			#add_filter( 'mce_external_languages', array( &$this, 'mce_localisation' ) );
		}

		/**
		 * mce_localisation
		 *
		 * @access public
		 * @param array $mce_external_languages
		 * @return array
		 */
		public function mce_localisation( $mce_external_languages ) {

			if ( file_exists( self :: $dir . 'lang/mce_langs.php' ) )
				$mce_external_languages[ 'inpsydeOembedVideoShortcode' ] = self :: $dir . 'lang/mce-langs.php';
			return $mce_external_languages;
		}
		/**
		 * load_options
		 *
		 * @access protected
		 * @return void
		 */
		protected function load_options() {

			if ( ! get_option( self :: OPTION_KEY ) ) {
				if ( empty( self :: $default_options ) )
					return;
				$this->options = self :: $default_options;
				add_option( self :: OPTION_KEY, $this->options , '', 'yes' );
			}
			else {
				$this->options = get_option( self :: OPTION_KEY );
			}
		}

		/**
		 * update_options
		 *
		 * @access protected
		 * @return bool True, if option was changed
		 */
		public function update_options() {
			return update_option( self :: OPTION_KEY, $this->options );
		}

		/**
		 * activation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function activate() {

		}

		/**
		 * deactivation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function deactivate() {

		}

		/**
		 * uninstallation
		 *
		 * @access public
		 * @static
		 * @global $wpdb, $blog_id
		 * @return void
		 */
		public static function uninstall() {
			global $wpdb, $blog_id;
			if ( is_network_admin() ) {
				if ( isset ( $wpdb->blogs ) ) {
					$blogs = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT blog_id ' .
							'FROM ' . $wpdb->blogs . ' ' .
							"WHERE blog_id <> '%s'",
							$blog_id
						)
					);
					foreach ( $blogs as $blog ) {
						delete_blog_option( $blog->blog_id, self :: OPTION_KEY );
					}
				}
			}
			delete_option( self :: OPTION_KEY );
		}
	}
}

