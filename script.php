<?php
/**
 * @package    Editor Switcher
 * @copyright  © 2021
 * @license    http://www.gnu.org/licenses/gpl.html
 * @author     Thomas Hunziker (www.bakual.net), Yoshiki Kozaki (www.joomler.net)
 * @link       https://www.bakual.net/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;

/**
 * Script to remove old language files in the global folder
 *
 * @since  2.0.0
 */
class PlgEditorsSwitcherInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $minimumJoomla = '3.10.0';
	/**
	 * A list of files to be deleted
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $deleteFiles = array();
	/**
	 * A list of folders to be deleted
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $deleteFolders = array();
	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since 2.0.0
	 */
	private $oldRelease;

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since 2.0.0
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'name', Factory::getDbo()->quote($parent->getName()));
			$this->oldRelease = $manifest['version'] ?? JVersion::MAJOR_VERSION . '.' . JVersion::MINOR_VERSION;
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to update the plugin
	 *
	 * @param   object  $parent  JInstallerAdapterPlugin class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '2.0.1', '<'))
		{
			// Cleanup language files in global language folder
			$this->deleteFiles[] = '/administrator/language/en-GB/en-GB.plg_editors_switcher.ini';
			$this->deleteFiles[] = '/administrator/language/en-GB/en-GB.plg_editors_switcher.sys.ini';
			$this->deleteFiles[] = '/administrator/language/fr-FR/fr-FR.plg_editors_switcher.ini';
			$this->deleteFiles[] = '/administrator/language/fr-FR/fr-FR.plg_editors_switcher.sys.ini';
			$this->deleteFiles[] = '/administrator/language/ja-JP/ja-JP.plg_editors_switcher.ini';
			$this->deleteFiles[] = '/administrator/language/ja-JP/ja-JP.plg_editors_switcher.sys.ini';
			$this->deleteFiles[] = '/administrator/language/ja-JU/ja-JU.plg_editors_switcher.ini';
			$this->deleteFiles[] = '/administrator/language/ja-JU/ja-JU.plg_editors_switcher.sys.ini';

			$this->removeFiles();
		}
	}
}
