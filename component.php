<?php
/**
 * @package		Xero 1.3.2
 * @subpackage	Updated: June 11 2015
 * @author		Joomlabamboo http://www.joomlabamboo.com
 * @copyright 	Copyright (C) Joomlabamboo, June 11 2015
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 2 or later;
 * @version		Xero - 1.3.2 | ZGF v4 1.3.2
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Constant used to check for allow access in wordpress and J
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


// Include main Zen Class
include JPATH_THEMES . '/' . TEMPLATE  . '/zengrid/zen.php';

// Setup the params
$zen = new zen4();

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$this->language  = $doc->language;
$this->direction = $doc->direction;

?>
<!DOCTYPE html>
<html class="contentpane-page" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
<?php $zen->loadBlock('head'); ?>
<!--[if lt IE 9]>
	<script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
<![endif]-->
</head>
<body class="contentpane modal">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
