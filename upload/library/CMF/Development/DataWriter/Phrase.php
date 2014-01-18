<?php

/**
 * Data writer for phrases.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_DataWriter_Phrase extends XFCP_CMF_Development_DataWriter_Phrase
{

	/**
	 * Helper to get the developer data output directory only if it is enabled
	 * and applicable to this situation.
	 *
	 * @return string
	 */
	protected function _getDevOutputDir()
	{
		return $this->_getDevOutputDirForAddonId($this->get('addon_id'));
	}

	/**
	 * Helper to get the developer data output directory for specified Add-On Id
	 * and applicable to this situation.
	 *
	 * @param  string $addOnId
	 * @return string
	 */
	protected function _getDevOutputDirForAddonId($addOnId = '')
	{
		$devel = XenForo_Application::get('cmfDevelopment');
		$languageId = $this->get('language_id');
		if (!$devel || !$addOnId)
		{
			return parent::_getDevOutputDir();
		}
		else if ($languageId == 0) //master language
		{
			if ($addOnId == 'XenForo')
			{
				return $devel['createOnImport'] ? $this->_getPhraseModel()->getPhraseDevelopmentDirectory() : parent::_getDevOutputDir();
			}
			else
			{
				return ($phrasePath = CMF_Development_Helper_File::getSubPathByAddOnId($addOnId, 'phrases')) ? $phrasePath : '';
			}
		}
		else
		{
			return ($phrasePath = CMF_Development_Helper_File::getAddOnLanguagePathByLanguageId($languageId, $addOnId)) ? $phrasePath : '';
		}
	}

	/**
	 * Writes the development file output to the specified directory. This will write
	 * each template into an individual file for easier tracking in source control.
	 *
	 * @param string $dir Path to directory to write to
	 * @throws XenForo_Exception
	 */
	protected function _writeDevFileOutput($dir)
	{
		parent::_writeDevFileOutput($dir);

		if ($this->isUpdate() && $this->isChanged('addon_id') && ($oldDir = $this->_getDevOutputDirForAddonId($this->getExisting('addon_id'))))
		{
			$this->_deleteExistingDevFile($oldDir);
		}
	}
}