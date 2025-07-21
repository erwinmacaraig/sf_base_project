<?php 
namespace App\MessageHandler;

use App\Message\PurchaseConfirmationNotification;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Mpdf\Mpdf;
use Knp\Snappy\Pdf;


class PurchaseConfirmationNotificationHandler implements MessageHandlerInterface
{
    public function __construct(private MailerInterface $mailer, private Pdf $pdf) 
    {
        
    }
    public function __invoke(PurchaseConfirmationNotification $notification)
    {
        // 1, Create a PDF
            echo "Creating a PDF Contract note <br />";
        
        $content = "<h1>Contract Note for order {$notification->getOrderId()}</h1>";
        $content .= '<p>Total price: <b>P14578.02</b></p>';

        $contractNotePdf = $this->pdf->getOutputFromHtml($content);


        // 2.  Email the contract note to the buyer
        $email = (new Email())
            ->from('erwin@test.com')
            ->to('erwin.macaraig@gmail.com')
            ->subject("Contract note for order " . $notification->getOrderId())
            ->text('Here is your contract note')
            ->attach($contractNotePdf, 'contract-note.pdf');

        $this->mailer->send($email);

    }
}