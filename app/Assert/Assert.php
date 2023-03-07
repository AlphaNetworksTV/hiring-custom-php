<?php

declare(strict_types=1);

namespace App\Assert;

use KamranAhmed\Faulty\Exceptions\BadRequestException;

class Assert extends \Webmozart\Assert\Assert
{
    /**
     * @param $message
     * @throws BadRequestException
     */
    protected static function reportInvalidArgument($message)
    {
        throw new BadRequestException($message);
    }
}
