<?php
/**
 * ua-parser
 *
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 *
 * Released under the MIT license
 */
namespace UAParser_a\Util;

use Composer\CaBundle\CaBundle;
use UAParser_a\Exception\FetcherException;

interface ReaderInterface
{
    public function test(string $line): bool;

    public function read(string $line): string;
}

class ApacheCommonLogFormatReader
{
    protected function getRegex(): string
    {
        return '@^
            (?:\S+)                                                 # IP
            \s+
            (?:\S+)
            \s+
            (?:\S+)
            \s+
            \[(?:[^:]+):(?:\d+:\d+:\d+) \s+ (?:[^\]]+)\]            # Date/time
            \s+
            \"(?:\S+)\s(?:.*?)                                      # Verb
            \s+
            (?:\S+)\"                                               # Path
            \s+
            (?:\S+)                                                 # Response
            \s+
            (?:\S+)                                                 # Length
            \s+
            (?:\".*?\")                                             # Referrer
            \s+
            \"(?P<userAgentString>.*?)\"                            # User Agent
        $@x';
    }
}

abstract class AbstractReader
{
    /** @var ReaderInterface[] */
    private static $readers = array();

    public static function factory(string $line): ReaderInterface
    {
        foreach (static::getReaders() as $reader) {
            if ($reader->test($line)) {
                return $reader;
            }
        }
    }

    private static function getReaders(): array
    {
        if (static::$readers) {
            return static::$readers;
        }

        static::$readers[] = new ApacheCommonLogFormatReader();

        return static::$readers;
    }

    public function test(string $line): bool
    {
        $matches = $this->match($line);

        return isset($matches['userAgentString']);
    }

    public function read(string $line): string
    {
        $matches = $this->match($line);

        if (!isset($matches['userAgentString'])) {
            throw ReaderException::userAgentParserError($line);
        }

        return $matches['userAgentString'];
    }

    protected function match(string $line): array
    {
        if (preg_match($this->getRegex(), $line, $matches)) {
            return $matches;
        }

        return [];
    }

    abstract protected function getRegex();
}

class Fetcher
{
    private $resourceUri = 'https://raw.githubusercontent.com/ua-parser/uap-core/master/regexes.yaml';

    /** @var resource */
    private $streamContext;

    public function __construct($streamContext = null)
    {
        if (is_resource($streamContext) && get_resource_type($streamContext) === 'stream-context') {
            $this->streamContext = $streamContext;
        } else {
            $this->streamContext = stream_context_create(
                array(
                    'ssl' => array(
                        'verify_peer' => true,
                        'verify_depth' => 10,
                        'cafile' => CaBundle::getSystemCaRootBundlePath(),
                        static::getPeerNameKey() => 'www.github.com',
                        'disable_compression' => true,
                    )
                )
            );
        }
    }

    public function fetch()
    {
        $level = error_reporting(0);
        $result = file_get_contents($this->resourceUri, null, $this->streamContext);
        error_reporting($level);

        if ($result === false) {
            $error = error_get_last();
            throw FetcherException::httpError($this->resourceUri, $error['message']);
        }

        return $result;
    }

    public static function getPeerNameKey(): string
    {
        return version_compare(PHP_VERSION, '5.6') === 1 ? 'peer_name' : 'CN_match';
    }
}

class Converter
{
    /** @var string */
    private $destination;

    /** @var Filesystem */
    private $fs;

    public function __construct(string $destination, Filesystem $fs = null)
    {
        $this->destination = $destination;
        $this->fs = $fs ?: new Filesystem();
    }

    /** @throws FileNotFoundException */
    public function convertFile(string $yamlFile, bool $backupBeforeOverride = true): void
    {
        if (!$this->fs->exists($yamlFile)) {
            throw FileNotFoundException::fileNotFound($yamlFile);
        }

        $this->doConvert(Yaml::parse(file_get_contents($yamlFile)), $backupBeforeOverride);
    }

    public function convertString(string $yamlString, bool $backupBeforeOverride = true): void
    {
        $this->doConvert(Yaml::parse($yamlString), $backupBeforeOverride);
    }

    protected function doConvert(array $regexes, bool $backupBeforeOverride = true): void
    {
        $regexes = $this->sanitizeRegexes($regexes);
        $data = "<?php\nreturn ".preg_replace('/\s+$/m', '', var_export($regexes, true)).';';

        $regexesFile = $this->destination.'/regexes.php';
        if ($backupBeforeOverride && $this->fs->exists($regexesFile)) {

            $currentHash = hash('sha512', file_get_contents($regexesFile));
            $futureHash = hash('sha512', $data);

            if ($futureHash === $currentHash) {
                return;
            }

            $backupFile = $this->destination . '/regexes-' . $currentHash . '.php';
            $this->fs->copy($regexesFile, $backupFile);
        }

        $this->fs->dumpFile($regexesFile, $data);
    }

    private function sanitizeRegexes(array $regexes): array
    {
        foreach ($regexes as $groupName => $group) {
            $regexes[$groupName] = array_map(array($this, 'sanitizeRegex'), $group);
        }

        return $regexes;
    }

    private function sanitizeRegex(array $regex): array
    {
        $regex['regex'] = str_replace('@', '\@', $regex['regex']);

        return $regex;
    }
}