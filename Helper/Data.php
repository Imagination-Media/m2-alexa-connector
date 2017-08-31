<?php
/**
 * Alexa API connector
 *
 * @author Fred Orosko Dias <fred@imaginemage.com>
 * @link https://imaginemaage.com/
 */

namespace ImaginationMedia\Alexa\Helper;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_APIKEY = 'imaginationmedia_alexa/connect/api_key';
    const XML_PATH_DEBUGMODE = 'imaginationmedia_alexa/connect/debugmode';

    protected $orderModel;
    protected $customerModel;
    protected $storeManager;

    public function __construct(
        Context $context,
        Order $orderModel,
        Customer $customerModel,
        StoreManagerInterface $storeManager
    ) {
        $this->orderModel = $orderModel;
        $this->customerModel = $customerModel;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIKEY, ScopeInterface::SCOPE_STORE);
    }

    public function getDebugStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEBUGMODE, ScopeInterface::SCOPE_STORE);
    }

    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    public function getTime($type)
    {
        $time = time();

        if ($type == 'from'){
            if ($this->getDebugStatus()) {
                $time = strtotime('07/01/2017');
            }
        } else {
            if ($this->getDebugStatus()) {
                $time = strtotime('07/27/2017');
            }
        }

        return date('Y-m-d', $time);
    }

    public function getSales()
    {
        $orders = $this->orderModel->getCollection()
            ->addFieldToFilter('created_at',array('gteq' => $this->getTime('from').' 00:00:00'))
            ->addFieldToFilter('created_at',array('lteq' => $this->getTime('to').' 23:59:59'))
            ->addExpressionFieldToSelect('im_grand_total', 'SUM(grand_total)', 'im_grand_total')
            ->addExpressionFieldToSelect('im_count', 'COUNT(entity_id)', 'im_count')
            ->addExpressionFieldToSelect('im_shipping_total', 'SUM(shipping_amount+shipping_tax_amount)', 'im_shipping_total');

        $data = $orders->getFirstItem();

        return 'Your customers made '.$data['im_count'].' orders today. Total amount of orders is '.round($data['im_grand_total']).' dollars.';
    }

    public function getCustomers()
    {
        $customers = $this->customerModel->getCollection()
            ->addFieldToFilter('created_at',array('gteq' => $this->getTime('from').' 00:00:00'))
            ->addFieldToFilter('created_at',array('lteq' => $this->getTime('to').' 23:59:59'))
            ->count();

        return 'You got ' . $customers . ' new customers today';
    }

    public function getRefunds()
    {
        $orders = $this->orderModel->getCollection()
            ->addFieldToFilter('status', Order::STATE_CLOSED)
            ->addFieldToFilter('created_at',array('gteq' => $this->getTime('from').' 00:00:00'))
            ->addFieldToFilter('created_at',array('lteq' => $this->getTime('to').' 23:59:59'))
            ->addExpressionFieldToSelect('im_total_refunded', 'SUM(total_refunded)', 'im_total_refunded')
            ->addExpressionFieldToSelect('im_count', 'COUNT(entity_id)', 'im_count');

        $data = $orders->getFirstItem();

        return 'Today you\'ve got ' . $data['im_count'] . ' refunds. Total is ' . round($data['im_total_refunded']) . ' dollars';
    }
}
