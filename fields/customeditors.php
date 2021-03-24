<?php
/**
 * @package     EditorSwitcher
 *
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2021 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 */
defined('JPATH_PLATFORM') or die();

JFormHelper::loadFieldClass('plugins');

/**
 * Renders an editors listfield
 *
 * @since  2.0
 */
class JFormFieldCustomEditors extends JFormFieldPlugins
{
	/**
	 * The field type.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $type = 'customeditors';

	/**
	 * Method remove the "switcher" editor plugin from the list.
	 *
	 * @return  array  An array of options.
	 *
	 * @since   2.0
	 */
	protected function getOptions()
	{
		$options = parent::getOptions();

		foreach ($options as $key => $option)
		{
			if ($option->value === 'switcher')
			{
				unset($options[$key]);
				break;
			}
		}

		return $options;
	}
}
