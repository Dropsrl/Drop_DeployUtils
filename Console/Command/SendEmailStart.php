<?php

namespace Drop\DeployUtils\Console\Command;

use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class SendEmailStart extends Command
{
    /**
     * @var \Drop\DeployUtils\Helper\Email
     */
    protected $sendEmail;

    /**
     * SendEmailStart constructor.
     * @param \Drop\DeployUtils\Helper\Email $sendEmail
     */
    public function __construct(
        \Drop\DeployUtils\Helper\Email $sendEmail
    ) {
        $this->sendEmail = $sendEmail;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('drop:deploy:sendalert')
            ->setDescription('Manda una mail per avvertire di un deploy in corso, se lanciato con la maintenance attiva');
    }

    /**
     * Send mail
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input,OutputInterface $output)
    {
        $result = $this->sendEmail->sendAlert(
            'drop_deploy_send_alert'
        );

        if($result) {
            $output->writeln('Deployment start email sent.');
        } else {
            $output->writeln('Deployment start email has NOT be sent. Check log for further details.');
        }
    }
}
