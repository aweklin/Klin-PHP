<?php

namespace Framework\Interfaces;

interface IMailClient {
    public function send(array $recipients = [], string $subject, string $body, array $attachments = [], array $ccs = [], array $bccs = [], string $senderName = '', string $senderEmail = '');
}