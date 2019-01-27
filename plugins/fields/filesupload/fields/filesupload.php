<?php
/**
 * @author          Denis Vorontsov
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldFilesUpload extends JFormField
{
    protected $type = 'FilesUpload';

    public function getInput()
    {
        //set session for authentication
        $session = JFactory::getSession();
        $settings = array(
            'destination' => $this->getAttribute('destination'),
            'filesize' => $this->getAttribute('filesize'),
            'acceptedformats' => $this->getAttribute('acceptedformats'),
            'filename_format' => $this->getAttribute('filename_format')
        );
        $session->set('filesupload', $settings);
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('var base = \'' . JURI::base() . '\'');
        JHtml::script(Juri::base() . 'plugins/fields/filesupload/assets/filesupload.js');
        $filesDir = $this->value;
        $jsonFiles = json_decode($filesDir);
        error_reporting(0);
        $fileNames = array_diff(scandir($jsonFiles->{'uploadDirPath'}), array('..', '.', 'index.html'));
        $fileLinks = array();
        if ($fileNames) {
            foreach ($fileNames as $name) {
                $fileLinks[] = '<a href="' . $jsonFiles->{'relDirPath'} . $name . '">'. $name .'</a>';
            }
        } else {
            $fileLinks = array();
        }
        echo '<table><tr><td><input type="file" multiple="multiple"  name="getFile" id="getFile"/></td><td></table>';
        $i = 0;
        if ($fileLinks) {
            foreach ($fileLinks as $link) {
                echo '<table><div id="file' . $i . '">' . $link . '</div></td></tr></table>';
                $i++;
            }
        }
        echo '<div id="iu_notice"></div>';
        echo '<input type="hidden" name="' . $this->name . '" id="setfieldval" value="' . $this->value . '" />';

        return;
    }
}