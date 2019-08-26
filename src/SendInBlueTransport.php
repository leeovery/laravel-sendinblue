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

        $message->setCc([]);
        $message->setBcc([]);

        $response = $this->client->sendTransacEmail(
            $this->buildPayload($message, $recipients)
        );

        $message->getHeaders()->addTextHeader(
            'X-SendInBlue-Transmission-ID', $response->getMessageId()
        );

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * @param  Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function getRecipients(Swift_Mime_SimpleMessage $message)
    {
        $recipients = [];

        foreach ((array) $message->getTo() as $email => $name) {
            $recipients['to'][] = compact('email', 'name');
        }

        foreach ((array) $message->getCc() as $email => $name) {
            $recipients['cc'][] = compact('email', 'name');
        }

        foreach ((array) $message->getBcc() as $email => $name) {
            $recipients['bcc'][] = compact('email', 'name');
        }

        return $recipients;
    }

    /**
     * @param  Swift_Mime_SimpleMessage  $message
     * @param  array                     $recipients
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
            'to'          => $recipients['to'],
            'cc'          => $recipients['cc'] ?? [],
            'bcc'         => $recipients['bcc'] ?? [],
            'htmlContent' => $message->getBody(),
            //'textContent' => '',
            'subject'     => $message->getSubject(),
        ]);
    }

    public function sibClient(): SMTPApi
    {
        return $this->client;
    }
}