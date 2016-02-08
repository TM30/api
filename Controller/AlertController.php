<?php namespace Controller;

use Data\Model\User;
use Nette\Mail\Message as Message;
use Nette\Mail\SendmailMailer as Mailer;

class AlertController
{
    private $netteMessage;
    private $netteMailer;
    private $user;

    public function __construct()
    {
        $this->netteMessage = new Message();
        $this->netteMailer = new Mailer();
        $this->user = new User();
    }

    /**
     * Tis function sends an email alert to this recipient with the given message  and subject.
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    public function sendEmailAlert($userType, $subject, $message)
    {
        if ($userType == "Technical")
            $users = $this->user->getPersistenceObject()->customQuery("select * from users where users.role != 2"); //Technical && ALl
        else
            $users = $this->user->getPersistenceObject()->customQuery("select * from users where users.role != 1"); //Operational && All

        if($users) {
            $this->netteMessage->setFrom("Seevas Application");
            foreach($users as $user)
                $this->netteMessage->addTo($user->email);
            $this->netteMessage->setSubject($subject)->setBody($message);
            $this->netteMailer->send($this->netteMessage);
        }
        return true;
    }

}
