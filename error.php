<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Since we have access define the zen constant
define('ZEN_ALLOW', 1);
// Let the framework know we are in Joomla
// Define the framework we are in
define('WP', 0);
define('JOOMLA', 1);

// Set the Theme Name
define('TEMPLATE', basename(dirname(__FILE__)));

// SET the site root
define( 'ROOT_PATH', JPATH_ROOT);
	
// Set the theme path
define( 'TEMPLATE_PATH', JPATH_THEMES.'/'.TEMPLATE.'/');
define( 'TEMPLATE_PATH_RELATIVE', 'templates/'.TEMPLATE.'/');
define( 'TEMPLATE_URI', JURI::base() . '/templates/'.TEMPLATE);
define('FRAMEWORK_PATH', TEMPLATE_PATH.'/zengrid');


if (!isset($this->error))
{
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}

// Check if in admin
$jinput = JFactory::getApplication()->input;
$admin = $jinput->get('admin');

if(!$admin) {
// Load template logic
if (!defined('TEMPLATE')) {
	define( 'TEMPLATE', basename(dirname(__FILE__)));
}

// Include main Zen Class
include JPATH_THEMES . '/' . $this->template  . '/zengrid/zen.php';

// Setup the params
$zen = new zen4();


if(	$zen->params->bodyfont =="-1" ||
		$zen->params->headingfont  =="-1" ||
		$zen->params->navfont =="-1" ||
		$zen->params->logofont =="-1" ||
		$zen->params->customfont =="-1"
	) {
	
		$fontarray = array();
		
		if(	$zen->params->bodyfont =="-1") {
			$fontarray[] = $zen->params->bodyfont_custom;
		}
		
		if(	$zen->params->headingfont =="-1") {
			$fontarray[] = $zen->params->headingfont_custom;
		}
		
		if(	$zen->params->navfont =="-1") {
			$fontarray[] = $zen->params->navfont_custom;
		}
		
		if(	$zen->params->logofont =="-1") {
			$fontarray[] = $zen->params->logofont_custom;
		}
		
		if(	$zen->params->customfont =="-1" && $zen->params->customfontselector !=="") {
			$fontarray[] = $zen->params->customfont_custom;
		}
		
		// Make array unique
		$fontarray = array_unique($fontarray);	
		$fonts = str_replace(' ', '+', implode('%7C', $fontarray));
	} 
	
// Get language and direction
$this->language  = $zen->doc->language;
$this->direction = $zen->doc->direction;?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" class="error-page">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template;?>/css/error.css" type="text/css" />
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=<?php echo $fonts;?>" type="text/css" />
	
	<?php
		$bodyfont = $zen->params->bodyfont;
		$headingfont = $zen->params->headingfont; 
	?>
	
	<style type="text/css">
	<?php 
		if($bodyfont == "-1") { ?>
				html > body {
					font-family: <?php echo $zen->cleanFonts($zen->params->bodyfont_custom); ?>;
					
				}
		<?php } else { ?>
				html > body {
					font-family: <?php echo $bodyfont ?>;
				}
		<?php } 
	
			if($headingfont == "-1") { 
				?>
				h1, h2, h3, h4, h5, h6, blockquote {
					font-family: <?php echo $zen->cleanFonts($zen->params->headingfont_custom); ?>;
				}
		<?php } else {  ?>
				h1, h2, h3, h4, h5, h6, blockquote {
					font-family: <?php echo $headingfont ?>;
				}
		<?php } ?>

	
	</style>

</head>
<body>
	<section id="logowrap" class="clearfix" role="banner">
		<div class="zen-row">
			<div class="zen-container">
				<?php 
					
					// Render the Search module
					$this->logomodules = JModuleHelper::getModules('logo');
									   
				    foreach ($this->logomodules as $logomodule)
				    {
				        $output = JModuleHelper::renderModule($logomodule, array('style' => 'zendefault'));
				        $params = new JRegistry;
				        $params->loadString($logomodule->params);
				        echo $output;
				    }
				?>
			</div>
		</div>
	</section>
	<section id="errorwrap">
		<div class="zen-row">
			<div class="zen-container">
				<div class="zg-col zg-col-12">
					<div class="padding-style">
						<h1>
							<?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
						</h1>
						
						<?php if (JModuleHelper::getModule('search')) : ?>
							<div id="searchbox">
								
								<p>
									<?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?> 
								</p>
								<?php $module = JModuleHelper::getModule('search'); ?>
								<?php echo JModuleHelper::renderModule($module); ?>
							</div><!-- end searchbox -->
						<?php endif; ?>
					</div>
					<h3><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></h3>
					<p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
					<ul>
						<li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
						<li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
						<li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
						<li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
					</ul>
					<div class="divider"></div>
					<div><!-- start gotohomepage -->
						<p>
						
						</p>
					</div><!-- end gotohomepage -->
					<h3>
						<?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>
					</h3>
				</div>
			</div>	
		</div>
	</section>
</body>
</html>
<?php } else {?>
<div id="admin-error">
	<div id="alert">
		<p><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
	</div>
	
	<style>
		#admin-error {
			border-radius:0 !important;
			min-height:74px;
			background:#00A8E6;
			border:none;
			padding:20px 0 0;
			text-align:center;
			color:#fff;
			text-shadow:none;
		}
		
		#zgfmessage {
			display: block !important;
		}
	
	</style>
</div>
<?php } ?>