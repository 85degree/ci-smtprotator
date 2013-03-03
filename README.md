ci-smtprotator
==============
https://github.com/apung/ci-smtprotator.git

Codeigniter Library for use to send email from multiple SMTP account. Each message use only one SMTP account.

## Example ##

    <?php

    class Email extends CI_Controller {

        public function __construct(){
            parent::__construct();
            $this->load->library('smtprotator');
        }


        // use it only once per server
        public function addserver(){
            $config['host'] = 'smtp.example.com';
            $config['user'] = 'your_username';
            $config['pass'] = 'your_pass';
            $this->smtprorator->addserver($config);
        }


        // sending email
        public function sendemail(){
            $server = $this->smtprotator->getserver();
            $chosen_smtp = $server['smtpid'];
            // if we choose to use SwiftMailer, then...
            $transport = Swift_SmtpTransport::newInstance($server['host'], $server['port'])
              ->setUsername($server['user'])
              ->setPassword($server['pass')
              ;
            $mailer = Swift_Mailer::newInstance($transport);

            // Create a message
            $message = Swift_Message::newInstance('Wonderful Subject')
              ->setFrom(array('john@doe.com' => 'John Doe'))
              ->setTo(array('receiver@domain.org', 'other@domain.org' => 'A name'))
              ->setBody('Here is the message itself')
              ;

            // Send the message
            $result = $mailer->send($message);

            // finally we must call the smtprotator::end class with
            $this->smtprotator->end($chosen_smtp);
        }
    }

