<?php

namespace Leeovery\LaravelSendInBlue;

use Swift_Mime_SimpleMessage;
use SendinBlue\Client\Api\SMTPApi;
use Illuminate\Mail\Transport\Transport;
use SendinBlue\Client\Model\SendSmtpEmail;

class SendInBlueTransport extends Transport
{
    /**
     * SendInBlue API Instance
     *
     * @var SMTPApi
     */
    protected $client;

    /**
     * Create a new SendInBlue transport instance.
     *
     * @param  SMTPApi  $client
     * @return void
     */
    public function __construct(SMTPApi $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $recipients = $this->getRecipients($message);

        dd($recipients);

        $message->setBcc([]);

        $response = $this->client->sendTransacEmail(
            $this->buildPayload($message, $recipients)
        );

        dd($response);

        $message->getHeaders()->addTextHeader(
            'X-SendInBlue-Transmission-ID', $response->getMessageId()
        );

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * @param  Swift_Mime_SimpleMessage  $message
     * @return SendSmtpEmail
     */
    private function buildPayload(Swift_Mime_SimpleMessage $message, $recipients): SendSmtpEmail
    {
        $from = $message->getSender() ?: $message->getFrom();

        return new SendSmtpEmail([
            'sender'      => [
                'name'  => current($from),
                'email' => key($from),
            ],
            'replyTo'     => [
                'name'  => current($from),
                'email' => key($from),
            ],
            'to'          => $recipients,
            'htmlContent' => $message->toString(),
            'subject'     => $message->getSubject(),
        ]);
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that SparkPost still respects CC, BCC headers in raw message itself.
     *
     * @param  Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function getRecipients(Swift_Mime_SimpleMessage $message)
    {
        $recipients = [];

        foreach ((array) $message->getTo() as $email => $name) {
            $recipients[] = compact('name', 'email');
        }

        foreach ((array) $message->getCc() as $email => $name) {
            $recipients[] = compact('name', 'email');
        }

        foreach ((array) $message->getBcc() as $email => $name) {
            $recipients[] = compact('name', 'email');
        }

        return $recipients;
    }
}