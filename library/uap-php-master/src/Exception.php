<?php
/**
 * ua-parser
 *
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 *
 * Released under the MIT license
 
 */

namespace UAParser_a\Exception;

use Exception;

use DomainException as BaseDomainException;

use InvalidArgumentException as BaseInvalidArgumentException;

class DomainException extends BaseDomainException
{
}


class InvalidArgumentException extends BaseInvalidArgumentException
{
    public static function oneOfCommandArguments(string ...$args): self
    {
        return new static(
            sprintf('One of the command arguments "%s" is required', implode('", "', $args))
        );
    }
}

class ReaderException extends DomainException
{
    public static function userAgentParserError(string $line): self
    {
        return new static(sprintf('Cannot extract user agent string from line "%s"', $line));
    }
}

class FileNotFoundException extends Exception
{
    public static function fileNotFound(string $file): self
    {
        return new static(sprintf('File "%s" does not exist', $file));
    }

    public static function customRegexFileNotFound(string $file): self
    {
        return new static(
            sprintf(
                'ua-parser cannot find the custom regexes file you supplied ("%s"). Please make sure you have the correct path.',
                $file
            )
        );
    }

    public static function defaultFileNotFound(string $file): self
    {
        return new static(
            sprintf(
                'Please download the "%s" file before using ua-parser by running "php bin/uaparser ua-parser:update"',
                $file
            )
        );
    }
}

class FetcherException extends DomainException
{
    public static function httpError(string $resource, string $error): self
    {
        return new static(
            sprintf('Could not fetch HTTP resource "%s": %s', $resource, $error)
        );
    }
}


