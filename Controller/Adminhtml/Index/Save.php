<?php

namespace UPSBG\Dashboard\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * Handles the admin save action for UPSBG Dashboard.
 */
class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Execute method
     *
     * Validates the form key and redirects with an error message if invalid.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $formKey = $this->getRequest()->getParam('form_key');

        if (!$formKey || !$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form key.'));
            return $this->_redirect('*/*/');
        }
    }
}
