<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceShipped extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->order=$data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Shipped',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data =$this->order->giftCode;
        $str = '';
        foreach ($data as $code) {
            $str=$str.$code->code."\n";
        }
        return new Content(
            text: 'mail.invoice',with: ['str' => $str]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
