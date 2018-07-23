<?php
/**
 * Created by PhpStorm.
 * User: katsu
 * Date: 19/04/2016
 * Time: 14:22
 */
namespace Magenest\EmailNotifications\Observer\NewReview;



use Magenest\EmailNotifications\Observer\Email\Email;

class NewReview extends Email
{


    public function execute(Observer $observer)
    {
        $reviewId = $observer->getObject()->getId();

        /** @var \Magento\Review\Model\Review $reviewModel */
        $reviewModel = $this->_reviewFactory->create();

        $detail =  $reviewModel->load($reviewId)->getDetail();
        $title = $reviewModel->load($reviewId)->getTitle();
        $productId = $reviewModel->load($reviewId)->getEntityPkValue();
        $nickname = $reviewModel->load($reviewId)->getNickname();

        $enable = $this->_scopeConfig->getValue(
            'emailnotifications_config/config_group_new_review/config_new_review_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable == 'yes') {
            $receiverList = $this->_scopeConfig->getValue(
                'emailnotifications_config/config_group_new_review/config_new_review_receiver',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
//            $receiverEmails =explode(';', $receiverList);
            foreach ($receiverList as $receiverEmail) {
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        'emailnotifications_config/config_group_new_review/config_new_review_template',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'nickname' => $nickname,
                            'productId' => $productId,
                            'store' => $this->_storeManager->getStore(),
                            'title' => $title,
                            'detail' => $detail
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            'emailnotifications_config/config_group_email_sender/config_email_sender',
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
}
