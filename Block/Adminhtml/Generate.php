<?php

namespace ImaginationMedia\Alexa\Block\Adminhtml;

class Generate extends \Magento\Config\Block\System\Config\Form\Field {
    
    protected $_template = 'generatecode.phtml';

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $originalData = $element->getOriginalData();
        $label = $originalData['button_label'];
        $this->addData(array(
            'button_label' => __($label),
            'button_url'   => $this->getUrl('alexa/index/index'),
            'html_id' => $element->getHtmlId(),
        ));

        return $this->_toHtml();
    }
}