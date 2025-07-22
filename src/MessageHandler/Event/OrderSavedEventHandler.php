<?php
namespace App\MessageHandler\Event;

use Knp\Snappy\Pdf;
use Symfony\Component\Mime\Email;
use App\Message\Event\OrderSavedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrderSavedEventHandler implements MessageHandlerInterface {
    public function __construct(private MailerInterface $mailer, private Pdf $pdf) 
    {
        
    }
    public function __invoke(OrderSavedEvent $event)
    {
        // throwing an exception manually
        throw new \RuntimeException('ORDER COULD NOT BE FOUND');

        // 1. Create a PDF
        $content = "<h1>Contract Note for order {$event->getOrderId()}</h1>";
        $content .= '<p>Total price: <b>P32589.02</b></p>';

        $contractNotePdf = $this->pdf->getOutputFromHtml($content);


        // 2.  Email the contract note to the buyer
        $email = (new Email())
            ->from('erwin@test.com')
            ->to('erwin.macaraig@gmail.com')
            ->subject("Contract note for order " . $event->getOrderId())
            ->text('Here is your contract note')
            ->attach($contractNotePdf, 'contract-note.pdf');

        $this->mailer->send($email);

    }

}