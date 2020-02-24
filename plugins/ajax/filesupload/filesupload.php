<?php

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAjaxFilesUpload extends JPlugin
{

    function onAjaxFilesUpload()
    {
        $returnedValues = array();
        $session = JFactory::getSession();
        $settings = $session->get('filesupload');
        if (!$settings) {
            jexit();
        }

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        // $fieldName = 'files';
        $files = $_FILES;
        if (!$files) {
            return (json_encode(array('error' => JText::_('Файлы не были выбраны или файлы некорректны'))));
        }
        $uniqDirectoryPostfix = uniqid();
        $relDirPath = '';
        $uploadDirPath = '';

        foreach ($files as $file) {
            if ($file['error'] > 0) {
                switch ($file['error']) {
                    case 1:
                        return (json_encode(array('error' => JText::_('Файл ' . $file['name'] . ' слишком большой (PHP INI ALLOWS)'))));

                    case 2:
                        return (json_encode(array('error' => JText::_('Файл ' . $file['name'] . ' слишком большой (HTML FORM ALLOWS)'))));

                    case 3:
                        return (json_encode(array('error' => JText::_('Ошибка частичной загрузки'))));

                    case 4:
                        return (json_encode(array('error' => JText::_('Файл не выбран'))));

                }
            }

            $fileSize = $file['size'];
            $mfs = $settings['filesize'] * 1000;
            $sts = $settings['filesize'] / 1000;
            if ($fileSize > $mfs) {
                return (json_encode(array('error' => JText::_('Файл ' . $file['name'] . ' больше разрешенных ' . $sts . 'MB'))));
            }

            $fileName = $file['name'];
            $uploadedFileNameParts = explode('.', $fileName);
            $uploadedFileExtension = array_pop($uploadedFileNameParts);

            $validFileExts = explode(',', $settings['acceptedformats']);

            $extOk = false;

            foreach ($validFileExts as $key => $value) {
                if (preg_match("/$value/i", $uploadedFileExtension)) {
                    $extOk = true;
                }
            }

            if ($extOk == false) {
                return (json_encode(array('error' => JText::_('Неверное расширение файла ' . $fileName))));
            }

            $fileTemp = $file['tmp_name'];

            $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,image/bmp,application/pdf,application/msword,application/rtf,application/vnd.ms-excel,application/vnd.ms-office,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/vnd.oasis.opendocument.spreadsheet';
            $validFileTypes = explode(",", $okMIMETypes);
            $fileType = mime_content_type ($file['tmp_name']);
            if (!in_array($fileType, $validFileTypes)) {
                return (json_encode(array('error' => JText::_('Неверный MIME-тип файла ' . $fileName))));
            }


            $fileName2 = explode('.', $fileName);
            $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName2[0]);
            if ($settings['filename_format'] == 0) {
                $fileName = $fileName . "_" . time();
            } else if ($settings['filename_format'] == 1) {
                $fileName = $fileName . "_" . rand(100000, 999999);
            } else if ($settings['filename_format'] == 2) {
                $fileName = $fileName . "_" . substr(md5(microtime()), rand(0, 26), 12);
            }

            $fileName = $fileName . "." . $fileName2[1];

            $path = $settings['destination'];
            if (!$path) {
                $path = 'uploads' . DIRECTORY_SEPARATOR . $uniqDirectoryPostfix;
            } else {
                $path = 'uploads' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $uniqDirectoryPostfix;
            }

            $relDirPath = JURI::root() . $path . DIRECTORY_SEPARATOR;
            $uploadDirPath = JPATH_SITE . DIRECTORY_SEPARATOR . $path;
            $uploadPath = $uploadDirPath . DIRECTORY_SEPARATOR . $fileName;
            $relPath = $relDirPath . $fileName;

            if (!JFile::upload($fileTemp, $uploadPath)) {
                return (json_encode(array('error' => JText::_('Ошибка перемещения файла ' . $fileName))));
            } else {
                $stubFile = $uploadDirPath . DIRECTORY_SEPARATOR . 'index.html';
                if (!file_exists($stubFile)) {
                    $fp = fopen($stubFile, "w");
                    fclose($fp);
                }
                $rtn = array('error' => false, 'filename' => $fileName, 'relPath' => $relPath);
                $returnedValues[] = json_encode($rtn, JSON_UNESCAPED_SLASHES);
            }
        }
        $returnedValues = array('relDirPath' => $relDirPath, 'uploadDirPath' => $uploadDirPath, 'files' => $returnedValues);
        $returnedValues = json_encode($returnedValues);
        return $returnedValues;
    }
}