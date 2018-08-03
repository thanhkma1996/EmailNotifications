<?php

namespace Magenest\EmailNotifications\Observer\NewSubscription;


use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;

class NewSubscription extends Email
{
        CONST Sub_customer = "sub_customer";
        CONST Unsub_customer = "unsub_customer";
        CONST Sub_receiver = "sub_receiver";
        CONST Sub_template = "sub_template";
        CONST Email_sender = "email_sender";
        CONST Unsub_enable = "unsub_enable";
        CONST Unsub_receiver = "unsub_receiver";
        CONST Unsub_template = "unsub_temlate";
    public function execute(Observer $observer)
    {
        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */

            $receiverList = $this->_scopeConfig->getValue(
                self::Sub_receiver,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Sub_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                           self::Sub_customer
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Email_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $receiverEmail
                    )->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }

            $receiverList = $this->_scopeConfig->getValue(
                self::Unsub_receiver,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Unsub_template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                           self::Unsub_customer
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Email_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $receiverEmail
                    )->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }
        }

}
