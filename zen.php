<?php
/**
 * @package     Zen Grid Framework v4, 1.4.1
 * @subpackage  Updated: March 10 2016
 * @author      Joomlabamboo http://www.joomlabamboo.com
 * @copyright   Copyright (C) Joomlabamboo, March 10 2016
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License version 2 or later;
 * @version     1.4.1
 */

// Check to ensure this file is within the rest of the framework
defined('ZEN_ALLOW') or die();



if(!class_exists('Zen4')) {



	class Zen4
	{


		/**
		 * The params instance
		 *
		 * @since  3.0.0
		 */
		public $joomla;


		public function __construct()
		{
			if(JOOMLA) {
				$this->joomla 	= JFactory::getDocument();
				$this->app      = JFactory::getApplication();
				$this->doc  	= JFactory::getDocument();
			}

			// Config params
			$this->template_id = self::getTemplateId();
			$this->params 	= self::getParams();
			$this->layout 	= $this->params->layout;
			$this->params 	= $this->params->params;

			// Theme params
			$this->theme = self::getThemeParams();
			$this->theme_files = $this->theme->files;
			$this->theme = $this->theme->settings;

			$this->template_path = TEMPLATE_PATH_RELATIVE;
			$this->libs 	= $this->template_path.'zengrid/libs/';
			$this->is_mobile = false;
			$this->is_tablet = false;
		}



		/**
		 * Returns a json decoded object
		 *
		 *
		 */


		public function get_json($path) {

			$file = TEMPLATE_PATH .$path;
			$settings = file_get_contents($file);
			$settings = stripslashes($settings);
			return json_decode($settings);
		}


		/**
		 * Get xml
		 *
		 *
		 */

		public function get_xml($path) {
			$path = TEMPLATE_PATH.$path;
			return simplexml_load_file($path);
		}




		/**
		 * Gets Template Params
		 *
		 *
		 */


		public function getParams() {

			$templateId = $this->template_id;

			if($templateId) {
				$settings = TEMPLATE_PATH .'settings/config/config-'.$templateId.'.json';
				$default = TEMPLATE_PATH .'settings/config/config-default.json';

				if(file_exists($settings)) {
					$settings = self::get_json('settings/config/config-'.$templateId.'.json');
					return $settings;
				} elseif(file_exists($default)) {
					$settings = self::get_json('settings/config/config-default.json');
					return $settings;
				}
				else {
					return self::get_json('settings/default-config.json');
				}
			} else {
				return self::get_json('settings/default-config.json');
			}
		}




		/**
		 * Gets Theme Data
		 * Used in debug mode to putput settings at bottom of the page
		 * Not referenced in general working of the theme
		 */


		public function getThemeParams() {

			$settings = TEMPLATE_PATH .'settings/themes/theme.'.$this->params->theme.'.json';

			if(file_exists($settings)) {
				return self::get_json('settings/themes/theme.'.$this->params->theme.'.json');
			} else {
				return self::get_json('settings/default-theme.json');
			}
		}



		/**
		 * Gets specified modules and loads the template spotlight file
		 *
		 *
		 */

		 public function rowClass($modules) {
		 	return trim($this->layout->{$modules}->{'classes'}->{'classes'});
		 }



		public function getModules($modules) {
			if(isset($this->layout->{$modules})) {
				$modules = $this->layout->{$modules}->{'positions'};
				$this->loadBlock('spotlight',$modules);
			}
		}




		/**
		 * Loads a specific module
		 *
		 *
		 */
		public function loadModule($module, $style='zendefault') {
			if(JOOMLA) {
				echo '<jdoc:include type="modules" name="'.$module.'" style="'.$style.'" />';
			} else {

				if($style =="zentabs") {
					return self::generateTabs($module);
				} elseif($module =="breadcrumbs") {
					echo zen_breadcrumbs();
				} else {
					return dynamic_sidebar($module);
				}
			}
		}



		/**
		 * Deprecated function for loading spotlights for old T3 themes
		 * Use GetModules above
		 *
		 */

		public function spotlight($row, $dummy) {
			if(isset($this->layout->{$row})) {
				$modules = $this->layout->{$row}->{'positions'};
				$this->loadBlock('spotlight',$modules);
			}
		}

		/**
		 * Routes the count module function through the usual Joomla count function
		 *
		 *
		 */

		public function countModules($module) {

			if(JOOMLA) {
				return $this->joomla->countModules($module);

			} else {
				$widgets = get_option('sidebars_widgets');

				if($module == "breadcrumb") {
					return 1;
				}
				elseif(isset($widgets[$module])) {
					$count = count($widgets[$module]);

					if($count > 0) return 12/$count;
				}
			}
		}



		/**
		 * Get the menu function
		 *
		 *
		 */

		public function getmenu($type= null) {
			if(WP){
				return wp_nav_menu( array( 'theme_location' => $type, 'menu_id' => $type.'-menu' ) );
			} else {

				if($this->joomla->countModules('menu')) {
					return '<jdoc:include type="modules" name="menu" style="simple" />';
				}
			}
		}




		/**
		 * Generate Tabs
		 *
		 *
		 */
		public function getWidgetTitles($module) {

			$sidebars_widgets = wp_get_sidebars_widgets();
			$widget_ids = $sidebars_widgets['tabs'];
			$titles = array();

			foreach( $widget_ids as $id ) {
			    $wdgtvar = 'widget_'._get_widget_id_base( $id );
			    $idvar = _get_widget_id_base( $id );
			    $instance = get_option( $wdgtvar );
			    $idbs = str_replace( $idvar.'-', '', $id );
			    $titles[$id] = $instance[$idbs]['title'];
			}

			return $titles;

		}




		/**
		 * Generate Tabs
		 *
		 *
		 */
		public function generateTabs($module) {

			$titles = self::getWidgetTitles($module);

			ob_start(); ?>
			<div class="tabbed-modules">
				<ul class="zen-nav-tabs nav nav-tabs">
					<?php foreach ($titles as $key => $title) { ?>
						<li>
							<a data-target="tab" href="widget-<?php echo $key;?>">
								<?php echo $title;?>
							</a>
						</li>
					<?php } ?>
				</ul>

				<div class="zen-tab-content">

						<?php dynamic_sidebar($module);?>

				</div>
			</div>
			<?php

			return ob_get_clean();



		}







		/**
		 * Mobile Detect
		 *
		 *
		 */

		 public function load_mobile_detect() {

		 	if($this->params->mobile_detect) {

	 			// Register the mobiledetect library if it's not already loaded
	 			if (!class_exists('Mobile_Detect')) {
	 				include TEMPLATE_PATH . 'zengrid/libs/mobiledetect/Mobile_Detect.php';
	 			}

		 		// register this->detect
		 		$this->detect = new Mobile_Detect;

		 		// Store these values since we already have the class
		 		$this->is_tablet = $this->detect->isTablet();
		 		$this->is_mobile = $this->detect->isMobile() && !$this->detect->isTablet() ? 1 : null;

		 	}
		 }



		 public function hide_on_device($modules) {

		 	if($this->is_tablet || $this->is_mobile) {
			 	$classes = self::rowClass($modules);
			 	$classes = explode(' ', $classes);

			 	$hide_on_tablet = 0;
			 	$hide_on_phone = 0;

			 	foreach ($classes as $key => $class) {

			 		if($class == "hidden-tablets") {
			 			$hide_on_tablet = 1;
			 		}

			 		if($class == "hidden-phones") {
			 			$hide_on_phone = 1;
			 		}
			 	}

			 	if($this->is_tablet && $hide_on_tablet) {

			 		return true;

			 	}

			 	if($this->is_mobile && $hide_on_phone) {
			 		return true;
			 	}
			 }
			 else {

			  	return false;

			 }

		 }


		 /**
		  * Get responsive classes from class object
		  *
		  *
		  */
		 function getResponsiveClasses($row) {

		 	$classes = explode(' ', $this->rowClass($row));
		 	$responsive = "";

		 	foreach ($classes as $key => $class) {
		 		$portion = substr($class, 0, 3);

		 		if($portion == "hid") {
		 			$responsive .= $class;
		 			$responsive .= ' ';
		 		}
		 	}

		 	return $responsive;
		 }



		/**
		 * Checks to see if modules should be published
		 *
		 *	Returns Boolean;
		 */

		public function checkSpotlight($row) {


			if(isset($this->layout->{$row})) {

				if($this->params->mobile_detect) {

					$hide_on_device = self::hide_on_device($row);
				}

				else {
					$hide_on_device = 0;
				}

				if(!$hide_on_device) {

					if(isset($this->layout->{$row}->{'positions'})) {

						$row = $this->layout->{$row}->{'positions'};

							foreach ($row as $module => $width) {

								if($module == "panel-trigger") {
									return true;
								}

								if($module == "maincontent") {
									return true;
								}

								elseif($module == "off-canvas-trigger") {
									return true;
								}

								elseif($module == "off-canvas-trigger-mobile") {
									return true;
								}

								elseif($module == "select-menu") {
									return true;
								}

								elseif($module == "toggle-menu") {
									return true;
								}

								elseif($module == "one-page-menu") {

									return true;
								}

								elseif($module == "social") {
									return true;
								}

								elseif($module == "social-mobile") {
									return true;
								}

								elseif(JOOMLA) {
									if ($this->joomla->countModules($module)) {
										return true;
									}
								}
								else {
									if ( is_active_sidebar($module) ) {
										return true;
									}

							}
						}
					}
				}
			}
		}



		/**
		 * Loads a template block
		 *
		 *
		 */

		public function loadBlock($file, $params = null, $classes = null) {

			$custompath = TEMPLATE_PATH . '/custom/blocks/'.$file.'.php';

			if(isset($this->theme_files->child)) {
				$childpath = TEMPLATE_PATH . 'child/'.$this->theme_files->child.'/blocks/'.$file.'.php';
			} else {
				$childpath = null;
			}
			$path = TEMPLATE_PATH . '/tpls/blocks/'.$file.'.php';

			if(file_exists($childpath)) {
				$modules = $params;
				include($childpath);
			}
			elseif(file_exists($custompath)) {
				$modules = $params;
				include($custompath);
			}
			elseif(file_exists($path)) {
				$modules = $params;
				include($path);
			}
			else {
				echo $file . ' block not found.<br />';
			}
		}





		/**
		 * Google Fonts
		 *
		 *
		 */


		public static function fonts($bodyFont, $headingFont, $navFont, $logoFont, $customFont, $bodyFont_custom, $headingFont_custom, $navFont_custom, $logoFont_custom,$customfont_custom) {

			if(
				$bodyFont == "-1" ||
				$headingFont == "-1" ||
				$navFont == "-1" ||
				$logoFont == "-1" ||
				$customFont == "-1"
			) {

			// Font array
			$myfonts = array();


			// Check to see if the font should be added to the array
			if($bodyFont == "-1") {
				$bodyFont = str_replace(" ", "+", $bodyFont_custom);
				$myfonts[] = "'$bodyFont'";
			}

			if($headingFont == "-1") {
				$headingFont = str_replace(" ", "+",$headingFont_custom);
				$myfonts[] = "'$headingFont'";
			}

			if($navFont == "-1") {
				$navFont = str_replace(" ", "+", $navFont_custom);
				$myfonts[] = "'$navFont'";
			}

			if($logoFont == "-1") {
				$logoFont = str_replace(" ", "+", $logoFont_custom);
				$myfonts[] = "'$logoFont'";
			}


			if($customFont == "-1") {
				$customFont = str_replace(" ", "+", $customfont_custom);
				$myfonts[] = "'$customFont'";
			}


			// Remove Duplicates
			$myfonts = array_unique($myfonts);

			// Remove comma from last font
			$lastfont = end($myfonts);


			ob_start(); ?>

			<script type="text/javascript">
			      WebFontConfig = {

			      google: {
			          families: [
			          	<?php foreach ($myfonts as $font) {echo $font; if (!($font == $lastfont)){echo ', ';}} ?>
			          ]}
			      };

			      (function() {
			        var wf = document.createElement('script');
			        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
			            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
			        wf.type = 'text/javascript';
			        wf.async = 'true';
			        var s = document.getElementsByTagName('script')[0];
			        s.parentNode.insertBefore(wf, s);
			      })();
			</script>

			<?php return ob_get_clean();

			}

		}



		/**
			 * Check system messages
			 * Forked from T3
			 *
			 * @return  boolean  The system message queue has any message or not
			 */
			function hasMessage()
			{
				// Get the message queue
				$app      = JFactory::getApplication();
				$input    =  $app->input;

				if($input->getCmd('option') == 'com_content'){
					$messages = $app->getMessageQueue();

					return !empty($messages);
				}

				return true;
			}





		/**
		 * Load fonts css declaration
		 * Used to load fotns if not using the fotn loader
		 *
		 */

		public function loadFonts() {

			if(	$this->params->bodyfont =="-1" ||
				$this->params->headingfont  =="-1" ||
				$this->params->navfont =="-1" ||
				$this->params->logofont =="-1" ||
				$this->params->customfont =="-1"
			) {

				$fontarray = array();

				if(	$this->params->bodyfont =="-1") {
					$fontarray[] = $this->params->bodyfont_custom;
				}

				if(	$this->params->headingfont =="-1") {
					$fontarray[] = $this->params->headingfont_custom;
				}

				if(	$this->params->navfont =="-1") {
					$fontarray[] = $this->params->navfont_custom;
				}

				if(	$this->params->logofont =="-1") {
					$fontarray[] = $this->params->logofont_custom;
				}

				if(	$this->params->customfont =="-1" && $this->params->customfontselector !=="") {
					$fontarray[] = $this->params->customfont_custom;
				}


				// Make array unique
				$fontarray = array_unique($fontarray);

				// Fonts that have subsets cant be concatenated without varied results
				// So we check to see if there is more than one font being supplied

				// Set a flag that gets removed
				// If subsets are found fonts found
				$concfonts = 1;

				if(!empty($fontarray) && count($fontarray) > 1) {

					// Next we check to see if it's using a subset
					$subset = array_filter($fontarray, function($fontarray){
								return strpos($fontarray, '&') !== false; }
					);

					//If we are using a subset
					// Dont implode the array but serve the fonts separately
					if(count($subset) > 0) {

						// Remove flag because we need to process fonts separately
						$concfonts = 0;

						foreach ($fontarray as $key => $font) {
							// Add the font declaration to an array
							$fonts[] = $this->doc->addStyleSheet('//fonts.googleapis.com/css?family='.$font);
						}

						// return the array of font declarations
						return $fonts;
					}
				}

				if($concfonts) {


					// No fonts with subsets being loaded so we can concatenate the
					// fonts into a single script call.

					$fonts = str_replace(' ', '+', implode('%7C', $fontarray));
					$fonts = str_replace(',', '%2C', $fonts);

					if(JOOMLA) {
						return $this->doc->addStyleSheet('//fonts.googleapis.com/css?family='.$fonts);
					} else {
						wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family='.$fonts);
						wp_enqueue_style( 'googleFonts');
					}

				}
			}

			else {

				return false;
			}
		}






		/**
		 * Clean Fonts
		 * Used to prepare font names in Font Loader
		 *
		 */
		public function cleanFonts($subject) {
			$font = explode(':', str_replace("+", " ", $subject));
			$font = strpos($font[0], ' ') ? ''.$font[0].'' : $font[0];
			$font = explode('&', $font);
			return '\'' . $font[0] . '\'';
		}



		/**
		 * 	Language attr
		 *
		 *
		 */

		public function lang_attributes() {

			if(JOOMLA) {
				return 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$this->joomla->language.'" lang="'.$this->joomla->language.'" dir="'.$this->joomla->direction.'"';
			} else {
				return language_attributes();
			}
		}

		/**
		 * 	Body Classes
		 *	Forked from T3
		 *
		 */

		public function bodyclass() {

				$pageclass = array();


				$pageclass[] = 'template-'.$this->template_id;


				if(WP) {
					$pageclass[] = implode(' ', get_body_class());
				} else {
					$input = JFactory::getApplication()->input;

					if($input->getCmd('option', '')){
						$pageclass[] = $input->getCmd('option', '');
					}
					if($input->getCmd('view', '')){
						$pageclass[] = 'view-' . $input->getCmd('view', '');
					}
					if($input->getCmd('layout', '')){
						$pageclass[] = 'layout-' . $input->getCmd('layout', '');
					}
					if($input->getCmd('task', '')){
						$pageclass[] = 'task-' . $input->getCmd('task', '');
					}
					if($input->getCmd('Itemid', '')){
						$pageclass[] = 'itemid-' . $input->getCmd('Itemid', '');
					}

					if($this->params->framework_enable) {
						$pageclass[] = $this->params->framework_version;
					}

					if(JFactory::getUser()->guest) {
						$pageclass[] = 'is-guest';
					} else {
						$pageclass[] = 'is-user';
					}

					$lang = JFactory::getLanguage();
					$pageclass[] = str_replace(' ', '-', $lang->getTag());

					$menu = JFactory::getApplication()->getMenu();

					if($menu){
						$active = $menu->getActive();
						$default = $menu->getDefault();

						if ($active) {
							if($default && $active->id == $default->id){
								$pageclass[] = 'home';
							}

							if ($active->params && $active->params->get('pageclass_sfx')) {
								$pageclass[] = $active->params->get('pageclass_sfx');
							}
						}
					}

					$pageclass[] = 'j'.str_replace('.', '', (number_format((float)JVERSION, 1, '.', '')));

				}

				if($this->params->framework_enable) {
					$pageclass[] = $this->params->framework_version;
				}

				if($this->params->enable_responsive) {
					$pageclass[] = 'responsive-enabled';
				} else {
					$pageclass[] = 'responsive-disabled';
				}

				if($this->params->stickynav) {
					$pageclass[] = 'sticky-enabled';
				}

				if($this->params->disable_stickynav) {
					$pageclass[] = 'disable-stickynav-on-mobile';
				}



				// Get the layout type being used eg lrm, mlr etc
				$main = self::getMainLayout($this->layout->main->positions);
				$pageclass[] = 'layout-' . $main['layout'];
				$pageclass[] = 'rendered-' . $main['rendered'];

				// Add collapse menu type
				$pageclass[] = $this->params->navcollapse_type.'-menu';


				// Add mobile detect classes
				if($this->params->mobile_detect) {

					if($this->is_tablet) {
						$pageclass[] = ' isTablet';
					}

					if($this->is_mobile) {
						$pageclass[] = ' isPhone';
					}

					if(!$this->is_tablet && !$this->is_mobile) {
						$pageclass[] = ' isDesktop';
					}

				}

				return implode(' ', $pageclass);
		}



		/**
		 * 	Hide main content
		 *	Checks if on front page and if hidemain is enabled
		 *
		 */


		public function hideMain() {

			$hideMain = 0;

			if(JOOMLA) {

				$lang = JFactory::getLanguage();
				$menu = $this->app->getMenu();
				$hideMain = 0;

				if(	$this->params->hidefrontpage &&
					($menu->getActive() == $menu->getDefault( $lang->getTag())))
				{

					$hideMain = 1;
				}
			} else {

				if(	$this->params->hidefrontpage && is_home())
				{

					$hideMain = 1;
				}
			}

			return $hideMain;
		}




		/**
		 * 	Loads a js or css file
		 *	Used to load custom.css
		 *
		 */


		public function load_asset($file, $type, $system=null) {

			// Check exists
			$path = TEMPLATE_PATH . '/'. $file .$type;

			if (file_exists($path)) {

				if($system) {
					if($type == ".css") {
						wp_enqueue_style($file,'/'.self::template_path() . $file .$type,'',null);
					} else {
						wp_enqueue_script($file,'/'.self::template_path() . $file .$type,'',null);
					}
				} else {

					if($type == ".css") {
						if(JOOMLA) {
							echo '<link rel="stylesheet" href="'.self::template_path() . $file .$type.'" type="text/css" />';
						} else {
							wp_enqueue_style($file,'/'.self::template_path() . $file .$type,'',null);
						}
					} else {
						if(JOOMLA) {
							echo '<link href="'.self::template_path() . $file .$type.'" type="text/css" />';
						} else {
							wp_enqueue_script($file,'/'.self::template_path() . $file .$type,'',null);
						}
					}

				}
			}
		}








		/**
		 * 	A global to get the template path
		 *	Used for cdn or standard url
		 *
		 */

		public function template_path() {

			if($this->params->enable_cdn && $this->params->cdn_url) {

				$cdn_url = rtrim($this->params->cdn_url, '/');
				return $cdn_url.'/';

			} else {

				return TEMPLATE_PATH_RELATIVE;

			}

		}




		/**
		 * 	Loads js assets
		 *
		 */

		public function load_js() {

			$theme = TEMPLATE_PATH . 'js/template-'.$this->template_id.'.js';

			$path = self::template_path();

				//Load the combined js file
				if($this->params->compressjs) {

					// Set the file extension
					if($this->params->gzip_js) {
						$ext = 'php';
					} else {
						$ext = 'js';
					}

					// Check if unique js file exists
					if (file_exists($theme)) {

						// Load unique file
						if(JOOMLA) {
							$this->doc->addScript($path . 'js/template-'. $this->template_id .'.'. $ext);
						} else {
							wp_enqueue_script('template-js', '/'.$path . 'js/template-'.$this->template_id.'.'.$ext, array('jquery'),null);
						}

					} else {
						if(JOOMLA) {
							// Load the fallback
							$this->doc->addScript($path . 'js/template.'.$ext);
						} else {
							wp_enqueue_script('template-js', '/'.$path . 'js/template.'.$ext, array('jquery'),null);
						}
					}
				}
				else {

					$assets = self::getassets();

					// Child theme
					if(isset($this->theme_files->child)) {
						if($this->theme_files->child !=="none" && $this->theme_files->child !=="") {
							if(file_exists(TEMPLATE_PATH . 'child/'.$this->theme_files->child.'/'.$this->theme_files->child.'.js')){
								$assets[] = '../child/'.$this->theme_files->child.'/'.$this->theme_files->child.'.js';
							}
						}
					}

					foreach ($assets as $key => $asset) {
						if(JOOMLA) {
							$this->doc->addScript($path.'js/'.$asset);
						} else {
							wp_enqueue_script(basename($asset), '/'.$path.'js/'.$asset, array('jquery'),null);
						}
					}

					if($this->params->navcollapse_type =="toggle") {
						if(JOOMLA) {
							$this->doc->addScript($path.'zengrid/libs/zengrid/js/meanmenu.js');
						} else {
							wp_enqueue_script('meanmenu', '/'.$path.'/zengrid/libs/zengrid/js/meanmenu.js', array('jquery'),null);
						}
					}

					if($this->params->navcollapse_type =="select") {
						if(JOOMLA) {
							$this->doc->addScript($path.'zengrid/libs/zengrid/js/jquery.resmenu.min.js');
						} else {
							wp_enqueue_script('resmenu.min', '/'.$path.'/zengrid/libs/zengrid/js/jquery.resmenu.min.js', array('jquery'),null);
						}
					}

					if($this->params->enable_animations) {

						if(JOOMLA) {
							$this->doc->addScript($path.'zengrid/libs/zengrid/js/wow.min.js');
						} else {

							wp_enqueue_script('wow.min', '/'.$path.'/zengrid/libs/zengrid/js/wow.min.js', array('jquery'),null);
						}
					}


					$custom_assets = array_filter(self::custom_assets());

					foreach ($custom_assets as $key => $asset) {

						if(JOOMLA) {
							$this->doc->addScript($asset);
						} else {
							wp_enqueue_script(basename($asset), $asset,null);
						}

					}

				}
			}




		/**
		 * 	Loads css assets
		 *
		 */

		public function load_css() {


				if(!$this->params->devmode) {

					// load template css is a developer option to bypass the theme selection and just load the template css


					if(!$this->params->load_template_css) {

						// Set the file extension
						if($this->params->gzip_css) {
							$ext = 'php';
						} else {
							$ext = 'css';
						}

						// Required if first run and theme is a preset
						$theme = str_replace('theme.[example]-', '', $this->params->theme);
						$theme = str_replace('presets/theme.[example]-', '', $this->params->theme);

						if(file_exists(self::template_path() . 'css/theme.'. $theme .'.'. $ext)) {
							$theme = self::template_path() . 'css/theme.'. $theme .'.'. $ext;
						} else {
							$theme = self::template_path() . 'css/theme.template.'. $ext;
						}

						if(JOOMLA) {
							// load the file
							$this->doc->addStyleSheet($theme);
						} else {
							wp_enqueue_style($theme,'',null);
						}
					}
					else {
						if(JOOMLA) {
							$this->doc->addStyleSheet(self::template_path() . 'css/template.css');
						} else {
							wp_enqueue_style($this->params->theme, '/'.self::template_path() . 'css/template.css','',null);
						}
					}

					// Print stylesheet
					$this->doc->addStyleSheet(self::template_path() . 'css/print.css', $type="text/css", $media="print");
				}
				else { ?>

						<?php if($this->params->framework_enable) {

							$framework = $this->params->framework_version; ?>

							<link rel="stylesheet/less" type="text/css" href="<?php echo $this->libs;?>frameworks/<?php echo $framework; ?>/less/<?php echo $framework; ?>.less" />

						<?php } ?>
								<link rel="stylesheet/less" type="text/css" href="<?php echo $this->libs;?>zengrid/less/animate/animate-library.less" />
								<link rel="stylesheet/less" type="text/css" href="<?php echo $this->libs;?>zengrid/less/fontawesome/font-awesome-devmode.less" />
								<link rel="stylesheet/less" type="text/css" href="<?php echo $this->template_path;?>less/variables-generated-devmode-<?php echo $this->template_id; ?>.less" />

						<script>
						less = {
						   env: "development",
						   logLevel: 0

						 };
						 </script>

						<script src="<?php echo $this->libs;?>lessjs/less.js" type="text/javascript"></script>

						<?php if($this->params->watch_mode) { ?>
							<script>less.watch();</script>
						<?php } ?>

				<?php }

		}



		function object2array($object) {
			return json_decode(json_encode($object),1);
		}

		/**
		 * 	Gets JS files listed in the assets file
		 *
		 *
		 */

		public function getassets($path = "") {
			$assets = $path.$this->template_path.'/settings/assets.xml';
			$assets = simplexml_load_file($assets);
			$assets=self::object2array($assets);
			$assets = $assets['js']['file'];

			if(isset($this->theme_files->child)) {

				$child_assets = $path.$this->template_path.'/child/'.$this->theme_files->child.'/assets.xml';

				if(file_exists($child_assets)) {
					$child_assets = simplexml_load_file($child_assets);
					$child_assets=self::object2array($child_assets);
					$child_assets = $child_assets['js']['file'];

					if(in_array('scripts.js', $child_assets)) {
						$key_search = array_search('scripts.js', $assets);
						unset($assets[$key_search]);
					}

					$assets = array_merge($assets, $child_assets);
					$assets = array_unique($assets);
				}
			}

			return $assets;
		}



		/**
		 * 	Gets custom assets listed in theme admin
		 *
		 *
		 */

		public function custom_assets() {
			$custom_assets = explode(',', $this->params->add_to_compressor);
			return $custom_assets;
		}



		/**
		 * 	Loads a module
		 *	Used for mega menu
		 *
		 */

		public static function module($module) {
			// load module
			$id    = intval($module);
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
				->from('#__modules AS m')
				->where('m.id = ' . $id)
				->where('m.published = 1');
			$db->setQuery($query);
			$module = $db->loadObject();

			//check in case the module is unpublish or deleted
			if ($module && $module->id) {
				$style   = 'zendefault';
				$content = JModuleHelper::renderModule($module, array(
					'style' => $style
				));

				return $content . "\n";
			}
		}




		/**
		 * 	Returns the template id
		 *
		 *
		 */
		public function getTemplateId() {

			if(JOOMLA) {
				// Joomla
				if(isset($this->app->getTemplate('template')->id)) {
					$templateId  = $this->app->getTemplate('template')->id;
				} else {
					// Mainly a work around for Virtuemart
					if(file_exists(TEMPLATE_PATH.'settings/default-id.json')) {
						$templateId = self::get_json('settings/default-id.json');
						$templateId = $templateId->id;
					} else {
						$templateId= 0;
					}
				}
			} else {
				// Wordpress

				$templateId = 'default';
				if ( is_front_page() ) {
					$templateId = 'frontpage';
				}elseif ( is_single() ) {
					$templateId = 'single';
				} elseif ( is_page() ) {
					$templateId = 'page';
				} elseif ( is_category() ) {
					$templateId = 'category';
				} elseif ( is_tag() ) {
					$templateId = 'tag';
				} elseif ( is_author() ) {
					$templateId = 'author';
				} elseif ( is_archive() ) {
					$templateId = 'archive';
				} elseif ( is_search() ) {
					$templateId = 'search';
				}
			}
			return $templateId;
		}





		/**
		 * 	Unsets a file
		 *
		 *
		 */

		public function unsetScript($file) {
			unset($this->doc->_scripts[$file]);
		}


		/**
		 * 	Unsets captionjs
		 *
		 *
		 */

			public function unsetCaptionJs() {

				if (isset($this->doc->_script['text/javascript'])) {
					$this->doc->_script['text/javascript'] = preg_replace('%jQuery\(window\)\.on\(\'load\',\s*function\(\)\s*{\s*new\s*JCaption\(\'img.caption\'\);\s*}\);\s*%', '', $this->doc->_script['text/javascript']);

					if (empty($this->doc->_script['text/javascript']))
						unset($this->doc->_script['text/javascript']);
				}

			}


		/**
		 * 	Move JS Down
		 *
		 *
		 */

		public function moveDownJs () {

			$bottomScript = '';
			if ($this->doc->_script['text/javascript']) {
				$bottomScript = '<script>'.$this->doc->_script['text/javascript'].'</script>';
				unset($this->doc->_script['text/javascript']);
				return $bottomScript;
			}
			unset($bottomScript);

		}


		/**
		 * 	Loads jQuery
		 *
		 *
		 */

			public function loadJquery() {

				if(JOOMLA) {
					echo JHtml::_('jquery.framework');
				}

			}



		/**
		 * 	Replace jQuery Version
		 *
		 *
		 */

		public function replace_key_function($array, $key1, $key2) {

			$keys = array_keys($array);

			$index = false;
			$i = 0;
			foreach($array as $k => $v){
				if($key1 === $k){
					$index = $i;
					break;
				}
				$i++;
			}

			if ($index !== false) {
				$keys[$index] = $key2;
				$array = array_combine($keys, $array);
			}
			return $array;

		}






		/**
		 * 	Function to return the main layout
		 *
		 *
		 */

		public function getMainLayout($main_layout) {
			$sidebar1=0;
			$sidebar2=0;
			$sidebar1_offset="";
			$sidebar2_offset="";
			$main_offset="";
			$layout_type="";
			$rendered_layout="";
			$source_order = 0;

			foreach ($main_layout as $module => $layout) {

					if($module == "maincontent") {
						$layout_type.="m";
						$rendered_layout .='m';

					} elseif ($module == "sidebar1") {
						$layout_type.="l";

						$sidebar1= $this->countModules('sidebar1')?1:0;

						if($sidebar1) {
							$rendered_layout .='l';
							$source_order = 1;
						}

					} elseif ($module == "sidebar2"){
						$layout_type.="r";
						$sidebar2= $this->countModules('sidebar2')?1:0;

						if($sidebar2) {
							$rendered_layout .='r';
							$source_order = 1;
						}
					}
			}

			return array('layout' => $layout_type, 'rendered' => $rendered_layout, 'sidebar1' => $sidebar1, 'sidebar2' => $sidebar2,'source_order' => $source_order);
		}



		/**
		 * Check social icon / network options
		 *
		 *
		 */


		public function check_social() {

			if($this->params->facebook || $this->params->twitter || $this->params->pinterest || $this->params->gplus || $this->countModules('panel') || self::check_extra_social()) {
				return 1;
			}

			else {
				return 0;
			}

		}



		public function check_extra_social() {

			if(isset($this->params->socialicons)) {
				$extra_social = (array)$this->params->socialicons;

				if(!empty($extra_social)) {
					return 1;
				}

				else {
					return 0;
				}
			} else {
				return 0;
			}

		}


		public function load_footer(){
			if(JOOMLA) {
				return;
			} else {
				return wp_footer();
			}
		}
	}
}