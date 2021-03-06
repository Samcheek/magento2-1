<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\LoginAsCustomerUi\Plugin\Button;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Plugin for \Magento\Backend\Block\Widget\Button\Toolbar.
 */
class ToolbarPlugin
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * ToolbarPlugin constructor.
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->authorization = $authorization;
        $this->url = $url;
    }

    /**
     * Add Login As Customer button.
     *
     * @param \Magento\Backend\Block\Widget\Button\Toolbar $subject
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePushButtons(
        Toolbar $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ):void {
        $order = false;
        $nameInLayout = $context->getNameInLayout();

        if ('sales_order_edit' == $nameInLayout) {
            $order = $context->getOrder();
        } elseif ('sales_invoice_view' == $nameInLayout) {
            $order = $context->getInvoice()->getOrder();
        } elseif ('sales_shipment_view' == $nameInLayout) {
            $order = $context->getShipment()->getOrder();
        } elseif ('sales_creditmemo_view' == $nameInLayout) {
            $order = $context->getCreditmemo()->getOrder();
        }
        if ($order) {
            if ($this->isAllowed()) {
                if (!empty($order['customer_id'])) {
                    $buttonUrl = $context->getUrl('loginascustomer/login/login', [
                        'customer_id' => $order['customer_id']
                    ]);
                    $buttonList->add(
                        'guest_to_customer',
                        [
                            'label' => __('Login As Customer'),
                            'onclick' => 'window.open(\'' . $buttonUrl . '\')',
                            'class' => 'reset'
                        ],
                        -1
                    );
                }
            }
        }
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    private function isAllowed(): bool
    {
        return (bool)$this->authorization->isAllowed('Magento_LoginAsCustomer::login_button');
    }
}
