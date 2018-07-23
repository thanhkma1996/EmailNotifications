<?php

namespace Magenest\EmailNotifications\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class AdditionalEmail
 */
class Status extends AbstractFieldArray
{

    protected function _prepareToRender()
    {
      $this->addColumn('changeform', ['label' => __('change from'), 'class' => 'required-entry']);
        $this->addColumn('changeto', ['label' => __('change to')]);
        $this->addColumn('email',['label' => __('Email'), 'size' => '50px', 'class' => 'required-entry validate-email']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Email');
    }
}