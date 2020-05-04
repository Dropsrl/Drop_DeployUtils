<?php

namespace Drop\DeployUtils\Helper;

use Magento\Framework\Mail\MessageInterface;
use Magento\Store\Model\ScopeInterface;

class Email {

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\App\MaintenanceMode
     */
    protected $maintenanceMode;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    const XML_PATH_DEPLOY_UTILS_GENERAL_EMAIL = 'deployutils/general/email';
    /**
     * @var \Drop\FatturazioneElettronica\Logger\Logger
     */
    private $logger;

    /**
     * Email constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\MaintenanceMode $maintenanceMode
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Drop\FatturazioneElettronica\Logger\Logger $logger
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\MaintenanceMode $maintenanceMode,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Drop\FatturazioneElettronica\Logger\Logger $logger
    )
    {
        $this->maintenanceMode = $maintenanceMode;
        $this->state = $state;
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->date = $date;
        $this->logger = $logger;
    }

    /**
     * @param $templateId
     * @param array $templateVariables
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendAlert($templateId, $templateVariables = []) {

        if (!$this->maintenanceMode->isOn()) {
            $this->logger->error('Maintenance is not enabled. Not sending deploy alert notification.');
            return false;
        }
        $to = $this->getEmailRecipients();
        if(empty($to)) {
            $this->logger->error('Empty email recipients.');
            return false;
        }

        $sender = [
            'name' => $this->scopeConfig->getValue('trans_email/ident_general/name'),
            'email' => $this->scopeConfig->getValue('trans_email/ident_general/email'),
        ];
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => \Magento\Store\Model\Store::DISTRO_STORE_ID
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVariables)
            ->setFrom($sender)
            ->AddTo($to)
            ->setReplyTo($sender['email'])
            ->getTransport();

        try {
            $transport->sendMessage();
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getEmailRecipients() {
        return explode(',', $this->getConfigValue(self::XML_PATH_DEPLOY_UTILS_GENERAL_EMAIL));
    }
}
