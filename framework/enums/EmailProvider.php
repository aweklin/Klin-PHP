<?php

namespace Framework\Enums;

enum EmailProvider {
    case Elastic;
    case SendGrid;
    case PHPMailer;
}