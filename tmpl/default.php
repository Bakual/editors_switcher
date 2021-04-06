<?php
/**
 * @package    Editor Switcher
 * @copyright  © 2021
 * @license    http://www.gnu.org/licenses/gpl.html
 * @author     Thomas Hunziker (www.bakual.net), Yoshiki Kozaki(www.joomler.net)
 * @link       https://www.bakual.net/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$currentEditor = $this->switcherEditor->_name;
$confirmation  = $this->params->get('confirmation', 1);

// Cookie parameters
$domain  = $this->app->get('cookie_domain', '');
$path    = $this->app->get('cookie_path', '/');
$expires = gmdate('r', time() + $this->params->get('cookie_days', 365) * 24 * 60 * 60);

// Load language string for JS
if ($confirmation)
{
	$this->loadLanguage('plg_editors_switcher', JPATH_PLUGINS . '/editors/switcher');
	Text::script('PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE');
}

$javascript = "document.addEventListener('DOMContentLoaded', function(){
	let elements = document.getElementsByClassName('switcher-selector');
	let changeEditor = function () {
		let attribute = this.getAttribute('data-confirmation');
		if(this.value != '" . $currentEditor . "' && (attribute == 0 || confirm(Joomla.JText._('PLG_EDITORS_SWITCHER_CONFIRM_MESSAGE')))){
			document.cookie = '" . $this->cookieName . "='+this.value+';domain=" . $domain . ";path=" . $path . ";expires=" . $expires . "';
			window.location.reload();
		} else {
			this.value = '" . $currentEditor . "';
			if (window.jQuery && jQuery(this).data('chosen')){
				jQuery(this).trigger('liszt:updated');
			}
		}
	};
	Array.from(elements).forEach(function (element) {
		element.addEventListener('change', changeEditor);
		if (window.jQuery && jQuery(element).data('chosen')){
			jQuery(element).on('change', changeEditor);
		}
	});
})";
$doc        = $this->app->getDocument();
$doc->addScriptDeclaration($javascript);

// Select Options
$options = array(
		'list.attr'   => array(
				'class'             => 'switcher-selector',
				'data-confirmation' => $confirmation,
		),
		'list.select' => $currentEditor,
);
?>
<div class="switcher-selector-wrapper btn-toolbar pull-right" style="margin-right:5px;">
	<?php echo JHtml::_('select.genericlist', $this->editors, 'switcheditor', $options); ?>
</div>
