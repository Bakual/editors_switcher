<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$params       = JPluginHelper::getPlugin('editors', 'switcher')->params;
$confirmation = $params->get('confirmation', 1);

//onchange
$jsOnchange = 'onchange="';
if ($confirmation)
{
	$this->loadLanguage('plg_editors_switcher', JPATH_PLUGINS . '/editors/switcher');

	// Load language string for JS
	Text::script('PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE');

	$jsOnchange .= 'if(this.options.selectedIndex != ' . $index . ' && confirm(Joomla.JText._(\'PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE\'))){';
	$jsOnchange .= 'jQuery(\'#editorswitcher-currentvalue\').value = this.options.selectedIndex;';
}

$domain     = $this->app->get('cookie_domain', '');
$path       = $this->app->get('cookie_path', '/');
$expires    = gmdate('r', time() + $params->get('cookie_days', 365) * 24 * 60 * 60);
$jsOnchange .= 'document.cookie = \'' . $this->cookieName . '=\'+this.value+\';domain=' . $domain . ';path=' . $path . ';expires=' . $expires . '\';';
$jsOnchange .= 'window.location.reload();';

if ($confirmation)
{
	$jsOnchange .= '} else {var curSelected = \'#jswitcheditor_chzn_o_\'+jswitcherEditors[document.id(\'editorswitcher-currentvalue\').value];
					if(jQuery(curSelected))jQuery(curSelected).trigger(\'mouseup\');}';
}
$jsOnchange .= '"';
?>
<div id="switcherSelector" class="btn-toolbar pull-right" style="margin-right:5px;">
	<input type="hidden" id="editorswitcher-currentvalue" value="<?php echo $current; ?>"/>
	<?php echo JHtml::_('select.genericlist', $editors, 'switcheditor' . ''
			, $jsOnchange, 'value', 'text', $current, 'jswitcheditor'); ?>
</div>
<?php
// Write html and move and init list index
$array  = json_encode($array);
$jsHead = "var jswitcherEditors = $array;window.addEvent('domready', function(){var btnwrap = $$('#editor-xtd-buttons .btn-toolbar');if(btnwrap.length > 0)"
		. " document.id('switcherSelector').inject(btnwrap[0]);	setTimeout(function(){var curSelected = '#jswitcheditor_chzn_o_'+$index;if(jQuery(curSelected)) jQuery(curSelected).trigger('mouseup');}, 1000);});";
JFactory::getDocument()->addScriptDeclaration($jsHead);
?>
