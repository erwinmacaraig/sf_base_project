<?php 
namespace App\MessageHandler;

use App\Message\PurchaseConfirmationNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PurchaseConfirmationNotificationHandler implements MessageHandlerInterface
{
    public function __invoke(PurchaseConfirmationNotification $notification)
    {
        // 1, Create a PDF
            echo "Creating a PDF Contract note <br />";
        // 2.  Email the contract note to the buyer
        echo "Emailing contract".  $notification->getOrder()->getBuyer()->getEmail() ."<br />";

    }
}