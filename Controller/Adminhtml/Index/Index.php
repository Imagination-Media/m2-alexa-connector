<?php
/**
 * Alexa API connector
 *
 * @author Fred Orosko Dias <fred@imaginemage.com>
 * @link https://imaginemaage.com/
 */

namespace ImaginationMedia\Alexa\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action {

    protected $resultJsonFactory;

    public function __construct
    (
        Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        return $result->setData(['token' => bin2hex(random_bytes(32))]);
    }
}
