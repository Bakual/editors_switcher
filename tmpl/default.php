<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


// Init list index
$array  = json_encode($array);
$jsHead = "var jswitcherEditors = $array;";
JFactory::getDocument()->addScriptDeclaration($jsHead);

$params       = JPluginHelper::getPlugin('editors', 'switcher')->params;
$confirmation = $params->get('confirmation', 1);

// onchange
$jsOnchange = 'onchange="';
if ($confirmation)
{
	$this->loadLanguage('plg_editors_switcher', JPATH_PLUGINS . '/editors/switcher');

	// Load language string for JS
	Text::script('PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE');

	$jsOnchange .= 'if(this.options.selectedIndex != ' . $index . ' && confirm(Joomla.JText._(\'PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE\'))){';
}

// Set cookie to new value
$domain     = $this->app->get('cookie_domain', '');
$path       = $this->app->get('cookie_path', '/');
$expires    = gmdate('r', time() + $params->get('cookie_days', 365) * 24 * 60 * 60);
$jsOnchange .= 'document.cookie = \'' . $this->cookieName . '=\'+this.value+\';domain=' . $domain . ';path=' . $path . ';expires=' . $expires . '\';';

// Reload page
$jsOnchange .= 'window.location.reload();';

if ($confirmation)
{
	$jsOnchange .= '} else {this.trigger(\'liszt:updated\');}';
}
$jsOnchange .= '"';
?>
<div id="switcherSelector" class="btn-toolbar pull-right" style="margin-right:5px;">
	<?php echo JHtml::_('select.genericlist', $editors, 'switcheditor' . ''
			, $jsOnchange, 'value', 'text', $current, 'jswitcheditor'); ?>
</div>
