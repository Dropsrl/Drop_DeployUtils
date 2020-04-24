<?php

namespace Drop\DeployUtils\Console\Command;

use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class SendEmailStop extends Command
{
    /**
     * @var \Drop\DeployUtils\Helper\Email
     */
    protected $sendEmail;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * SendEmailStop constructor.
     * @param \Drop\DeployUtils\Helper\Email $sendEmail
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Drop\DeployUtils\Helper\Email $sendEmail,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->sendEmail = $sendEmail;
        $this->directoryList = $directoryList;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('drop:deploy:endsendalert')
            ->setDescription('Send an email to warn of an ongoing deployment, if launched with active maintenance');
    }

    /**
     * Send mail
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->sendEmail->sendAlert(
            'drop_end_deploy_send_alert',
            ['composerlog' => $this->getComposerLogFileContent()]
        );

        if($result) {
            $output->writeln('End of deployment email sent.');
        } else {
            $output->writeln('End of deployment email has NOT sent. Check log for further details.');
        }
    }

    /**
     * @return string
     */
    protected function getComposerLogFileContent() {
        $logPath = $this->directoryList->getRoot() . '/var/log/composer.log';
        if (file_exists($logPath) && is_readable($logPath)) {
            return  $this->cleanComposerFileLog(file_get_contents($logPath));
        }
        return '';
    }

    private $searchSubstrings = [
        'warning',
        'COMPOSER_DEV_MODE',
        'drop:deploy:',
        'suggests installing',
        'is abandoned',
        'Never run composer inside magento',
        "Module '",
        'schema recurring'
    ];

    /**
     * Clean composer log file output
     *
     * @param $composerLog
     * @return string|string[]
     */
    protected function cleanComposerFileLog($composerLog) {
        $formattedLog = [];
        foreach (explode(PHP_EOL, $composerLog) as $line) {
            if ($this->strposArray($line, $this->searchSubstrings) !== FALSE) {
                continue;
            }
            $formattedLog[] = $line;
        }

        return implode('<br/>', $formattedLog);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool|false|int
     */
    protected function strposArray($haystack, $needle) {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $what) {
            if(($pos = strpos($haystack, $what))!==false) return $pos;
        }
        return false;
    }

}
