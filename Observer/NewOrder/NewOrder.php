<?php
namespace Magenest\EmailNotifications\Observer\NewOrder;

use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;
class NewOrder extends Email
{

    CONST Order_code = "order_code";
    CONST Coupon_order = "coupon_order";
    CONST Order = "order";
    CONST Coupon = "coupon";
    CONST Coupon_receive = "coupon_receive";
    CONST Coupon_template = "coupon_template";
    CONST Coupon_sender = "coupon_sender";
    CONST Order_sender = "order_sender";
    CONST Order_template = "order_template";
    CONST Email_sender = "email_sender";
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $orderModel */

            $receiverList = $this->_scopeConfig->getValue(
                self::Coupon_receive,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Coupon_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            self::Order_code
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Coupon_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo($receiverList)->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }


            $receiverList = $this->_scopeConfig->getValue(
              self::Order_sender,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
                try {
                    $template_id = $this->_scopeConfig->getValue(
                       self::Order_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            self::Coupon_order,
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Email_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo($receiverList)->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }

}
