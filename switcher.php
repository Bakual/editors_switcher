<?php
/**
 *            Plugin Editor Switcher
 * @version            2.0.0
 * @package            Editor Switcher
 * @copyright          Copyright (C) 2007-2012 Joomler!.net. All rights reserved.
 * @license            http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author             Yoshiki Kozaki(www.joomler.net)
 * @link               http://www.joomler.net/
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * Editor Switcher Plugin
 *
 * @since 1.0
 */
class plgEditorSwitcher extends CMSPlugin
{
	/**
	 * A Registry object holding the parameters for the plugin
	 *
	 * @var    Registry
	 * @since  2.0
	 */
	public $params = null;

	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  2.0
	 */
	protected $app;

	/**
	 * The selected Editor class
	 *
	 * @var    CMSPlugin
	 * @since  2.0
	 */
	protected $switcherEditor;

	/**
	 * List of available editors
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $editors;

	/**
	 * The name of the cookie
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $cookieName = 'editorswitchercurrent';

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 *
	 * @since       2.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$editor = $this->app->input->cookie->get($this->cookieName, 'switcher');

		if ($editor == 'switcher')
		{
			$editor = $this->params->get('default_editor', 'tinymce');
		}

		if (file_exists(JPATH_PLUGINS . '/editors/' . $editor . '/' . $editor . '.php')
			&& PluginHelper::isEnabled('editors', $editor))
		{
			$plugin = PluginHelper::getPlugin('editors', $editor);
			$this->setSwitcherEditor($subject, $plugin);
		}
		else
		{
			// Get first editor found
			$plugins = PluginHelper::getPlugin('editors');
			$plugin  = null;

			foreach ($plugins as $v)
			{
				if ($v->name != 'switcher')
				{
					$plugin = $v;
					break;
				}
			}

			if ($plugin)
			{
				$this->setSwitcherEditor($subject, $plugin);
			}
			else
			{
				$this->app->enqueueMessage(Text::_('PLG_EDITOR_SWITCHER_EDITORWASNOTFOUND'), 'warning');
			}
		}
	}

	/**
	 * Create the selected editor
	 *
	 * @param object $subject
	 * @param object $plugin
	 *
	 * @since 1.0
	 */
	private function setSwitcherEditor($subject, $plugin)
	{
		$editor = $plugin->name;

		require_once JPATH_PLUGINS . '/editors/' . $editor . '/' . $editor . '.php';
		$classname = 'plgEditor' . ucfirst($editor);
		$lang      = Factory::getLanguage();

		// Load language if not already done.
		if (!$lang->getPaths('plg_editors_' . $editor))
		{
			$lang->load('plg_editors_' . $editor, JPATH_ADMINISTRATOR)
			|| $lang->load('plg_editors_' . $editor, JPATH_PLUGINS . '/editors/' . $editor);
		}

		$this->switcherEditor = new $classname($subject, (array) $plugin);
	}

	/**
	 * Initialises the Editor.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onInit()
	{
		if (is_callable(array($this->switcherEditor, 'onInit')))
		{
			$this->switcherEditor->onInit();
		}
	}

	/**
	 * Get the editor content
	 *
	 * @param string $id The name of the editor
	 *
	 * @return  string
	 *
	 * @since      1.0
	 *
	 * @deprecated 4.0 Use directly the returned code
	 */
	public function onGetContent($id)
	{
		if (is_callable(array($this->switcherEditor, 'onGetContent')))
		{
			return $this->switcherEditor->onGetContent($id);
		}

		return '';
	}

	/**
	 * Set the editor content
	 *
	 * @param string $id   The name of the editor
	 * @param string $html The html to place in the editor
	 *
	 * @return  string
	 *
	 * @since      1.0
	 *
	 * @deprecated 4.0 Use directly the returned code
	 */
	public function onSetContent($id, $html)
	{
		if (is_callable(array($this->switcherEditor, 'onSetContent')))
		{
			return $this->switcherEditor->onSetContent($id, $html);
		}

		return '';
	}

	/**
	 * Copy editor content to form field
	 *
	 * @param string $id The name of the editor
	 *
	 * @return  string
	 *
	 * @since      1.0
	 *
	 * @deprecated 4.0 Use directly the returned code
	 */
	public function onSave($id)
	{
		if (is_callable(array($this->switcherEditor, 'onSave')))
		{
			return $this->switcherEditor->onSave($id);
		}

		return '';
	}

	/**
	 * Display the editor area.
	 *
	 * @param string  $name    The name of the editor area.
	 * @param string  $content The content of the field.
	 * @param string  $width   The width of the editor area.
	 * @param string  $height  The height of the editor area.
	 * @param int     $col     The number of columns for the editor area.
	 * @param int     $row     The number of rows for the editor area.
	 * @param boolean $buttons True and the editor buttons will be displayed.
	 * @param string  $id      An optional ID for the textarea. If not supplied the name is used.
	 * @param string  $asset   The object asset
	 * @param object  $author  The author.
	 * @param array   $params  Associative array of editor parameters.
	 *
	 * @return  string
	 * @since 1.0
	 *
	 */
	public function onDisplay(
		$name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (is_callable(array($this->switcherEditor, 'onDisplay')))
		{
			$return = $this->switcherEditor->onDisplay($name, $content, $width, $height, $col, $row, $buttons, $id, $asset, $author, $params);
			$return .= $this->setEditorSelector();

			return $return;
		}

		return '';
	}

	/**
	 * Create the selector of editors
	 *
	 * @staticvar   null $selector
	 *
	 * @return string
	 *
	 * @since       2.0
	 */
	private function setEditorSelector()
	{
		static $selector = null;

		if (is_null($selector))
		{
			$db         = JFactory::getDbo();
			$authGroups = JFactory::getUser()->getAuthorisedGroups();
			ArrayHelper::toInteger($authGroups);

			$query = $db->getQuery(true);
			$query->select($db->qn('element') . ' AS value');
			$query->select('CONCAT(UCASE(SUBSTRING(' . $db->qn('element')
				. ', 1, 1)), SUBSTRING(' . $db->qn('element') . ', 2)) AS text');
			$query->from($db->qn('#__extensions'));
			$query->where($db->qn('folder') . ' = ' . $db->q('editors'));
			$query->where($db->qn('type') . ' = ' . $db->q('plugin'));
			$query->where($db->qn('enabled') . ' = 1');
			$query->where($db->qn('element') . ' <> ' . $db->q('switcher'));
			$query->where($db->qn('access') . ' IN (' . implode(',', $authGroups) . ')');
			$query->order($db->qn('ordering'));
			$query->order($db->qn('name'));

			$db->setQuery($query);
			$this->editors = $db->loadObjectList();

			if (count($this->editors) < 2)
			{
				$selector = '';

				return $selector;
			}

			// Render the select field
			ob_start();
			include PluginHelper::getLayoutPath('editors', 'switcher');
			$selector = ob_get_clean();
		}

		return $selector;
	}

	/**
	 * Inserts html code into the editor
	 *
	 * @param string $name The name of the editor
	 *
	 * @return  string
	 *
	 * @since      1.0
	 *
	 * @deprecated 4.0 Code is loaded in the init script
	 */
	public function onGetInsertMethod($name)
	{
		if (is_callable(array($this->switcherEditor, 'onGetInsertMethod')))
		{
			return $this->switcherEditor->onGetInsertMethod($name);
		}

		return '';
	}
}
