<?php

namespace ImaginationMedia\Alexa\Controller\Index;

use ImaginationMedia\Alexa\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action {

    protected $request;
    protected $dataHelper;
    protected $resultJsonFactory;

    public function __construct(
        ActionContext $context,
        Http $request,
        Data $dataHelper,
        JsonFactory $resultJsonFactory
    ){
        parent::__construct($context);
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $return = 'Error';
        $result = $this->resultJsonFactory->create();
        $storedToken = $this->dataHelper->getApiKey();
        $requestedToken = $this->request->getParam('token');
        $requestedAction = $this->request->getParam('action');
        if (!empty($storedToken)) {
            if (!empty($requestedToken)) {
                if (!empty($requestedAction)) {
                    if ($requestedToken == $storedToken) {
                        switch ($requestedAction) {
                            case 'sales':
                                $return = $this->dataHelper->getSales();
                                break;
                            case 'customers':
                                $return = $this->dataHelper->getCustomers();
                                break;
                            case 'refunds':
                                $return = $this->dataHelper->getRefunds();
                                break;
                            default:
                                $return = 'Undefined API call';
                                break;
                        }
                    } else {
                        $return = 'Token is incorrect';
                    }
                } else {
                    $return = 'No action in query';
                }
            } else {
                $return = 'No token provided in query';
            }
        } else {
            $return = 'No token saved to store';
        }

        return $result->setData(['result' => $return]);
    }
}