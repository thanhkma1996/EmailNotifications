<?php

namespace Magenest\EmailNotifications\Observer\NewRegistration;


use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;


class NewRegistration extends  Email
{
    CONST Reg_Customer = "regis_customer";
    CONST Receive = "reg_receive";
    CONST Template = "reg_template";
    CONST Email_sender = "email_sender";
    public function execute(Observer $observer)
    {

            $receiverList = $this->_scopeConfig->getValue(
                self::Receive,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::Template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            self::Reg_Customer
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
