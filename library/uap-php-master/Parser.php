<?php
/**
 * ua-parser
 *
 * Copyright (c) 2011-2013 Dave Olsen, http://dmolsen.com
 * Copyright (c) 2013-2014 Lars Strojny, http://usrportage.de
 *
 * Released under the MIT license
 */
namespace UAParser_a;

class Parser_a 
{
    /** @var DeviceParser */
    private $deviceParser;

    /** @var OperatingSystemParser */
    private $operatingSystemParser;

    /** @var UserAgentParser */
    private $userAgentParser;

    /** Start up the parser by importing the data file to $this->regexes */
    public function __construct()
    {
        global $regex_array;
        $this->regexes = $regex_array;;
        $this->deviceParser = new DeviceParser($this->regexes);
        $this->operatingSystemParser = new OperatingSystemParser($this->regexes);
        $this->userAgentParser = new UserAgentParser($this->regexes);
    }

    /** Sets up some standard variables as well as starts the user agent parsing process */
    public function parse(string $userAgent, array $jsParseBits = array()): Client
    {
        $client = new Client($userAgent);

        $client->ua = $this->userAgentParser->parseUserAgent($userAgent, $jsParseBits);
        $client->os = $this->operatingSystemParser->parseOperatingSystem($userAgent);
        $client->device = $this->deviceParser->parseDevice($userAgent);

        return $client;
    }
    
}
