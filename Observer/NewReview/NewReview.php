<?php
/**
 * Created by PhpStorm.
 * User: katsu
 * Date: 19/04/2016
 * Time: 14:22
 */
namespace Magenest\EmailNotifications\Observer\NewReview;



use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\Observer;

class NewReview extends Email
{
    CONST Review = "review";
    CONST RV_Receive = "rv_receive";
    CONST RV_Template = "rv_template";
    CONST Email_sender = "email_sender";
    public function execute(Observer $observer)
    {
        $reviewId = $observer->getObject()->getId();

        /** @var \Magento\Review\Model\Review $reviewModel */



            $receiverList = $this->_scopeConfig->getValue(
                self::RV_Receive,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        self::RV_Template,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                           self::Review,
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            self::Email_sender,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $receiverList
                    )->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }

}
