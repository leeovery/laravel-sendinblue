<?php

namespace Leeovery\LaravelSendInBlue\Tests;

use Swift_Message;
use Orchestra\Testbench\TestCase;
use SendinBlue\Client\Api\SMTPApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\CreateSmtpEmail;
use Leeovery\LaravelSendInBlue\SendInBlueTransport;

class MailSendInBlueTransportTest extends TestCase
{
    public function testSend()
    {
        $message = new Swift_Message('Foo subject', 'Bar body');
        $message->setSender('myself@example.com');
        $message->setTo('me@example.com');
        $message->setCc('you@example.com');

        $client = $this->getMockBuilder(SMTPApi::class)
                       ->setMethods(['sendTransacEmail'])
                       ->disableOriginalConstructor()
                       ->getMock();

        $transport = new SendInBlueTransport($client);

        $input = new SendSmtpEmail([
            'sender'      => [
                'name'  => null,
                'email' => 'myself@example.com',
            ],
            'replyTo'     => [
                'name'  => null,
                'email' => 'myself@example.com',
            ],
            'to'          => [
                ['name' => null, 'email' => 'me@example.com'],
            ],
            'cc'          => [
                ['name' => null, 'email' => 'you@example.com'],
            ],
            'bcc'         => [],
            'htmlContent' => $message->getBody(),
            'subject'     => 'Foo subject',
        ]);

        $client->expects($this->once())
               ->method('sendTransacEmail')
               ->with($input)
               ->willReturn(new CreateSmtpEmail(['messageId' => '123']));

        $transport->send($message);
    }
}