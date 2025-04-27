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
// Use this file to publish information for your template 
// in the overview area.

defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Instantiate $zgf
$zgf = new zen();

$zgfversion = $zgf->get_xml('zengrid/zen.xml');

?>

<div class="preview-left">
	<img class="templatePreview" src="<?php echo PATH_FROM_ADMIN;?>/template_preview.png" alt="Template Preview" />
</div>
<div class="preview-right">
	<h3>Buildr is a do it yourself template builder for Joomla3+</h3>
	<p>Buildr gives you full control over module positions, padding,. margins, colours, responsive behaviour and more,. Don't miss the detailed documentation and demo themes for ideas on how to put this incredibly flexible theme to use.</p>
	
	<h3>Useful Links</h3>
	<p>
		<a target="_blank" class="uk-button uk-button-primary" href="http://www.joomlabamboo.com/joomla-templates/buildr">Template Features</a>
		<a target="_blank"  class="uk-button uk-button-primary" href="http://docs.joomlabamboo.com/joomla-templates/buildr-documentation">Template Documentation</a>

		<a target="_blank"  class="uk-button uk-button-primary" href="http://www.joomlabamboo.com/index.php?option=com_kunena&view=category&catid=690&Itemid=215">Template Support</a>
	</p>
	
	<h3>Associated Extensions</h3>
	<p>
		<a target="_blank"  class="uk-button uk-button-primary" href="http://docs.joomlabamboo.com/zen-grid-framework-4/menus/Zen-menu-plugin.html">Zenmenu</a> 
		<a target="_blank"  class="uk-button uk-button-primary" href="http://docs.joomlabamboo.com/zen-grid-framework-4/theme/Using-shortcodes.html">Zen Shortcodes</a>
	</p>

	
	<h3>Built with the <?php echo $zgfversion->name;?></h3>
	<p><?php echo $zgfversion->description;?></p>
	<p><a target="_blank"  href="<?php echo $zgfversion->link;?>">View Zen Grid Framework V5 Features</a></p>
	<p>
	<strong>Version:</strong> <?php echo $zgfversion->version;?> | <strong>Release Date:</strong> <?php echo $zgfversion->date;?> | <strong>Changelog:</strong> <a target="_blank"  href="<?php echo $zgfversion->changelog;?>">View</a></p>
</div>



