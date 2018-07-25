<?php

namespace Magenest\EmailNotifications\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class AdditionalEmail
 */
class Status extends AbstractFieldArray
{
    /**
     * @var OrderStatus
     */
    protected $_orderStatusRenderer;

    /**
     * @return OrderStatus|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getStatusRenderer()
    {
        if (!$this->_orderStatusRenderer) {
            $this->_orderStatusRenderer = $this->getLayout()->createBlock(
                \Magenest\EmailNotifications\Block\Adminhtml\Form\Field\OrderStatus::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_orderStatusRenderer->setClass('order_status_select');
        }
        return $this->_orderStatusRenderer;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('changeform', ['label' => __('change from'),'size'=>'3000px', 'class' => 'required-entry', 'renderer' => $this->_getStatusRenderer()]);
        $this->addColumn('changeto', ['label' => __('change to'),'size'=>'3000px', 'class' => 'required-entry', 'renderer' => $this->_getStatusRenderer()]);
        $this->addColumn('email',['label' => __('Email'),'size'=>'3000px', 'class' => 'required-entry validate-email']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Email');
    }
}