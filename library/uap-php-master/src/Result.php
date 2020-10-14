<?php
/**
 * ua-parser
 *
 * Copyright (c) 2011-2013 Dave Olsen, http://dmolsen.com
 * Copyright (c) 2013-2014 Lars Strojny, http://usrportage.de
 *
 * Released under the MIT license
 */
namespace UAParser_a\Result;

use UAParser_a\Result;

abstract class AbstractSoftware
{
    /** @var string */
    public $family = 'Other';

    public function toString(): string
    {
        return $this->family;
    }
}

abstract class AbstractVersionedSoftware extends AbstractSoftware
{
    abstract public function toVersion(): string;

    public function toString(): string
    {
        return implode(' ', array_filter(array($this->family, $this->toVersion())));
    }

    protected function formatVersion(?string ...$args): string
    {
        return implode('.', array_filter($args, 'is_numeric'));
    }
}


class OperatingSystem extends AbstractVersionedSoftware
{
    /** @var string */
    public $major;

    /** @var string */
    public $minor;

    /** @var string */
    public $patch;

    /** @var string */
    public $patchMinor;

    public function toVersion(): string
    {
        return $this->formatVersion($this->major, $this->minor, $this->patch, $this->patchMinor);
    }
}

class UserAgent extends AbstractVersionedSoftware
{
    /** @var string */
    public $major;

    /** @var string */
    public $minor;

    /** @var string */
    public $patch;

    public function toVersion(): string
    {
        return $this->formatVersion($this->major, $this->minor, $this->patch);
    }
}

class Device extends AbstractSoftware
{
    /** @var string */
    public $brand;

    /** @var string */
    public $model;
}

namespace UAParser_a;

class Client
{
    /** @var UserAgent */
    public $ua;

    /** @var OperatingSystem */
    public $os;

    /** @var Device */
    public $device;

    /** @var string */
    public $originalUserAgent;

    public function __construct(string $originalUserAgent)
    {
        $this->originalUserAgent = $originalUserAgent;
    }

    public function toString(): string
    {
        return $this->ua->toString().'/'.$this->os->toString();
    }
}




