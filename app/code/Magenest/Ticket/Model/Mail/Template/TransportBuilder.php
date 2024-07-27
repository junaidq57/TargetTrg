<?php
/**
 * Mail Template Transport Builder
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Ticket\Model\Mail\Template;

use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;

class TransportBuilder extends FrameworkTransportBuilder
{
    /**
     * @param $file
     * @return $this
     */
    public function createAttachment($file)
    {
        $this->message->createAttachment(
            $file,
            'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            'ticket.pdf'
        );

        return $this;
    }
}
