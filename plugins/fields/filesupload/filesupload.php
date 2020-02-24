<?php

defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);


class PlgFieldsFilesUpload extends FieldsPlugin
{
    public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);
        

        if (!$fieldNode)
        {
            return $fieldNode;
        }

        $form->addFieldPath(JPATH_PLUGINS . '/fields/filesupload/fields');
        $fieldNode->setAttribute('type', 'filesupload');

        return $fieldNode;
    }
}
