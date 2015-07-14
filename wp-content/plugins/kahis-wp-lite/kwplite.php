<?php
/*
Plugin Name: Kahi's WP Lite
Plugin URI: http://kahi.cz/wordpress/wp-lite-plugin/
Description: Make WordPress look thin.
Author: Peter Kahoun
Version: 0.8.2
Author URI: http://kahi.cz
*/

// @todo next: other selector-boxes [20]

class kwplite {
	const DEV = false;

	// Descr: full name. used on options-page, ...
	static $full_name = 'Kahi\'s WP Lite';

	// Descr: short name. used in menu-item name, ...
	static $short_name = 'WP Lite';

	// Descr: abbreviation. used in textdomain, ...
	// Descr: must be same as the name of the class
	static $abbr = 'kwplite';

	// Descr: path to this this file
	// filled automatically
	static $dir_name;

	// Descr: settings: names => default values
	// Descr: in db are these settings prefixed with abbr_
	// Required if using self::$settings!
	static $settings = array (
		'menuitems' => '',
		'elements_to_hide' => array(),
		'custom_css' => '',
		'userlevel' => false,
	);

	// to store some stuff in original shape ($menu, $submenu)
	static $remember = array();

	/**
	 * Selectors and descriptions of all hide-able elements
	 * @uses apply_filters() 'kwplite_selectors'
	 *
	 * @var array
	 **/
	static $selectors = array(
'Post' => array(
	'slug' => array('#edit-slug-box,label[for="slugdiv-hide"]', 'Slug (URL)'),
	'separator' => '',
	'media' => array('#media-buttons', 'Media buttons'),
	'media-add_image' => array('#add_image', 'Media buttons: add image'),
	'media-add_video' => array('#add_video', 'Media buttons: add video'),
	'media-add_audio' => array('#add_audio', 'Media buttons: add audio'),
	'media-add_media' => array('#add_media', 'Media buttons: add media'),
	'separator1' => '',
	'content'  => array('#postdivrich', 'Content'),
	'content-tabs' => array('#editor-toolbar', 'Content: Visual/HTML tabs'),
	'content-footer1' => array('tr.mceLast', 'Content: Visual editor footer 1 ("Path")'),
	'content-footer2' => array('table#post-status-info', 'Content: Visual editor footer 2'),
	'content-footer2a' => array('td#wp-word-count', 'Content: Visual editor footer 2: word count'),
	'content-footer2b' => array('td.autosave-info', 'Content: Visual editor footer 2: autosave info'),
	'separator2' => '',
	'box-excerpt' => array('#postexcerpt,label[for="postexcerpt-hide"]', 'Excerpt'),
	'box-comments-management' => array('#commentsdiv,label[for="commentsdiv-hide"]', 'Comments management'),
	'box-comments' => array('#commentstatusdiv,label[for="commentstatusdiv-hide"]', 'Comments status'),
	'box-trackbacks' => array('#trackbacksdiv,label[for="trackbacksdiv-hide"]', 'Trackbacks'),
	'box-customfields' => array('#postcustom,label[for="postcustom-hide"]', 'Custom fields'),
	'box-categories'  => array('#categorydiv,label[for="categorydiv-hide"]', 'Categories'),
	'box-tags' => array('#tagsdiv-post_tag,label[for="tagsdiv-post_tag-hide"]', 'Tags'),
	'box-author' => array('#authordiv', 'Author'),
	'box-revisions' => array('#revisionsdiv', 'Revisions'),
	'box-postimage' => array('#postimagediv', 'Post image'),
	'separator3' => '',
	'pub-visibility'  => array('#visibility', 'Publishing: privacy/visibility'),
	'pub-curtime' => array('.curtime', 'Publishing: date'),
	'separator4' => '',
	'pageparentdiv' => array('#pageparentdiv', 'Page details (parent page, template and order)'),
),
// @todo add boxes added by plugins - see More Fields plugin - search $wp_meta_boxes in more-fields-manage-pages.php
// @todo remember - add selectors for quickedit
// @maybe submit patch to wp core - add ids for sub-fields in #pageparentdiv

'Dashboard' => array(
	'right-now' => array('#dashboard_right_now,label[for="dashboard_right_now-hide"]', 'Right now'),
	'right-now-theme' => array('.versions > p', 'Right now: Theme info'),
	'right-now-version' => array('#wp-version-message', 'Right now: Version info'),
	'recent-comments' => array('#dashboard_recent_comments,label[for="dashboard_recent_comments-hide"]', 'Recent comments'),
	'incoming-links' => array('#dashboard_incoming_links,label[for="dashboard_incoming_links-hide"]', 'Incoming links'),
	'plugins' => array('#dashboard_plugins,label[for="dashboard_plugins-hide"]', 'Recommended plugins'),
	'quickpress' => array('#dashboard_quick_press,label[for="dashboard_quick_press-hide"]', 'QuickPress'),
	'drafts' => array('#dashboard_recent_drafts,label[for="dashboard_recent_drafts-hide"]', 'Recent drafts'),
	'news1' => array('#dashboard_primary,label[for="dashboard_primary-hide"]', 'WordPress development blog'),
	'news2' => array('#dashboard_secondary,label[for="dashboard_secondary-hide"]', 'WordPress other news'),
),
// @maybe right-now subfields: comments (8 cells), category, tags, pages, posts (per 2 cells)

'General' => array(

	'header-logo' => array('#header-logo', 'Header: WordPress logo'),
	'site-visit-button' => array('#site-visit-button', 'Header: Visit site button'),
	'favorite-actions' => array('#favorite-actions', 'Header: Favourite actions'),
	'turbo' => array('.turbo-nag', 'Header: Turbo link'),
	'plugins-number' => array('#adminmenu .update-plugins', 'Menu: Plugins number'),
	'separator1' => '',
	'tab-settings' => array('#show-settings-link', 'Tab: Settings'),
	'tab-help' => array('#contextual-help-link-wrap', 'Tab: Help'),
	'update-nag' => array('#update-nag', 'Update possibility message'),
	'separator2' => '',
	'footer' => array('#footer', 'Footer'),
	'footer-by' => array('#footer-left', 'Footer: version info (left part)'),
	'footer-version' => array('#footer-upgrade', 'Footer: links (right part)'),
),

'Links' => array(

	'name-descr' => array('#namediv p', 'Name: note'),
	'address-descr' => array('#addressdiv p', 'Address: note'),
	'separator1' => '',
	'description' => array('#descriptiondiv', 'Description'),
	'description-descr' => array('#descriptiondiv p', 'Description: note'),
	'separator2' => '',
	'categories' => array('#linkcategorydiv', 'Categories'),
	'target' => array('#linktargetdiv', 'Target'),
	'xfn' => array('#linkxfndiv', 'XFN (relations)'),
	'advanced' => array('#linkadvanceddiv', 'Advanced'),
	'separator3' => '',
	'privacy' => array('Privacy #misc-publishing-actions', 'Privacy'),
),

'Other' => array(
	// @todo Edit Comments (spam marking and folder)
	// @todo Edit Media (Alternate text, Description, Caption)
)
);




	// Descr: initialization. filling main variables, preparing, hooking
	// Descr: constructor replacement (this class is designed to be used as static). calling the initialization: see the end.
	public static function Init () {
		if (self::DEV) error_reporting(E_ALL);

		// set self::$dir_name
		// example: my-plugin
		$t = str_replace('\\', '/', dirname(__FILE__));
		self::$dir_name = trim(substr($t, strpos($t, '/plugins/')+9), '/');

		// load translation
		// @todo: generate .pot (very low priority)
		// load_plugin_textdomain(self::$abbr, 'wp-content/plugins/' . self::$dir_name . '/languages/');

		// prepare settings
		self::prepareSettings();

		// hooking
		register_uninstall_hook(__FILE__, array(self::$abbr, 'uninstall'));
		add_action('admin_init', array (self::$abbr, 'admin_init'));
		add_action('admin_head', array (self::$abbr, 'admin_head'));
		add_action('admin_menu', array (self::$abbr, 'admin_menu'));
	}


	// ====================  WP hooked functions  ====================

	// Hook: Action: admin_init
	// fires custom hooks
	// modifies global variables $menu and $submenu
	public static function admin_init ($content) {

		// fire custom hooks
		self::$selectors = apply_filters('kwplite_selectors', self::$selectors);



		// modify global variables $menu and $submenu
		global $menu, $submenu;
		if (!isset($menu) OR !isset($submenu)) return;

		// backup original content of menu (will be needed on options-page)
		// @maybe rewrite: don't modify $menu, just the menu-output cycle (possible? simple?)
		self::$remember['menu'] = $menu;
		self::$remember['submenu'] = $submenu;


		// maybe terminate functíon (if user-level restriction applies)
		// @maybe fix DRY
		global $current_user;
		if (self::$settings['userlevel'] AND $current_user->user_level >= self::$settings['userlevel'])
			return;


		// remove hidden items from $menu
		foreach ($menu as $key => $menuitem) {
			// if (array_key_exists('1'.md5($menuitem[2]), (self::$settings['menuitems'])) {
			if (isset(self::$settings['menuitems']['1'.md5($menuitem[2])])) {
				unset($menu[$key]);
				continue;
			}
		}

		// remove hidden items from $submenu (on both levels)
		foreach ($submenu as $parent_id => $items) {
			if (isset(self::$settings['menuitems']['1'.md5($parent_id)])) {
				unset($submenu[$parent_id]);
				continue;
			} else {
				foreach ($items as $id => $menuitem) {
					if (isset(self::$settings['menuitems']['2'.md5($menuitem[2])])) {
						unset($submenu[$parent_id][$id]);
						continue;
					}
				}
			}
		}

	}


	// Hook: Special: admin_menu
	// Descr: adds own item into menu in administration
	public static function admin_menu () {

		add_submenu_page( // for add_menu_page - skip first parameter
			'options-general.php',
			$page_title = self::$short_name,
			$menu_title = self::$short_name,
			$access_level = 'level_10',
			$file = __FILE__,
			$function = array (self::$abbr, 'adminPage')
			);

	}


	/**
	 * WP Hook (action): admin_head. Outputs my CSS depending on settings.
	 */
	public static function admin_head () {

?>

<!-- by plugin: <?php echo self::$full_name; ?> -->
<style type="text/css">

	/* options-page interface */

	#kwplite div.col {
		float:left; margin-right:20px;
		min-width:200px; max-width:350px; padding-top:20px;
	}

		#kwplite div.col .col-content {
			height:380px; padding-right:2em; overflow-y:scroll;
		}

		#kwplite div.col .col-content.tall {
			height:780px;
		}

			#kwplite div.col h3 {
				margin-top:0;
			}

			#kwplite ul li.separated {
				margin-top:1.5em;
			}

			#kwplite ul ul {
				margin-left:1.7em;
			}


	#kwplite p.submit {
		clear: both; padding-top:30px;
	}

	#kwplite .cleaner {
		clear:both;
	}


	/* post-editing elements hiding xxx */

<?php

	global $current_user;

	if ((!self::$settings['userlevel']) OR (self::$settings['userlevel'] AND $current_user->user_level < self::$settings['userlevel'])) {

		if (isset(self::$settings['elements_to_hide'])) {
			foreach (self::$settings['elements_to_hide'] as $s_group_name => $s_group_data) {
				if (is_array($s_group_data)) {
					foreach ($s_group_data as $e_id => $e_on) {
						if (isset(self::$selectors[$s_group_name][$e_id])) {
							echo self::$selectors[$s_group_name][$e_id][0] . ',';
						}
					}
				}
			}
		}

	} ?> #non_ex_ist_ing {display:none;}


	/* custom css */

<?php
	if ((!self::$settings['userlevel']) OR (self::$settings['userlevel'] AND $current_user->user_level < self::$settings['userlevel'])) {
		echo self::$settings['custom_css'];
	}
?>

</style>

<?php
	}


	// ====================  WP administration pages  ====================

	/**
	 * Requires own admin-page (plugin's settings)
	 * @return void
	 */
	public static function adminPage () {
		require_once 'admin-page.php';
	}


	// ====================  WP-general code  ====================

	/**
	 * Loads settings from db (wp_options) and stores them to self::$settings[setting_name_without_plugin_prefix]
	 * Settings-names are in db prefixed with "{self::$abbr}_", keys in $settings aren't. Very reusable.
	 * @see self::$settings
	 * @return void
	 */
	public static function prepareSettings () {

		foreach (self::$settings as $name => $default_value) {
			if (false !== ($option = get_option(self::$abbr . '_' . $name))) {
				self::$settings[$name] = $option;
			} else {
				// do nothing, let there be the default value
			}
		}

		// self::debug(self::$settings);

	}


	/**
	 * WP Hook: Uninstallation. Removes all plugin's settings. Very reusable.
	 * @return void
	 */
	public static function uninstall () {
		foreach (self::$settings as $name => $value) {
			delete_option(self::$abbr.'_'.$name);
		}
	}


	/**
	 * Outputs content given as first parameter. Enhanced replacement for var_dump().
	 * @param mixed Variable to output
	 * @param string (Optional) variable description
	 * @return void
	 */
	public static function debug($var, $descr = false) {

		if ($descr) echo '<p style="background:#666; color:#fff"><b>'.$descr.':</b></p>';

		echo '<pre style="max-height:300px; overflow-y:auto">'.htmlSpecialChars(var_export($var, true)).'</pre>';

	}


} // end of class


// ====================  Initialize the plugin  ====================
if (is_admin())
	kwplite::Init();