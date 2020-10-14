<?php
/**
 * ua-parser
 *
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 *
 * Released under the MIT license
 */



use UAParser_a\Exception\InvalidArgumentException;
use UAParser_a\Exception\ReaderException;
use UAParser_a\Result\Client;
use UAParser_a\Util\Logfile\AbstractReader;
use UAParser_a\Parser;


class ParserCommand
{
    protected function configure()
    {
        $this
            ->setName('ua-parser:parse')
            ->setDescription('Parses a user agent string and dumps the results.')
            ->addArgument(
                'user-agent',
                null,
                InputArgument::REQUIRED,
                'User agent string to analyze'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = Parser::create()->parse($input->getArgument('user-agent'));

        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return 0;
    }
}


class UpdateCommand
{
    /** @var string */
    private $resourceDirectory;

    public function __construct($resourceDirectory)
    {
        $this->resourceDirectory = $resourceDirectory;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ua-parser:update')
            ->setDescription('Fetches an updated YAML file for ua-parser and overwrites the current PHP file.')
            ->addOption(
                'no-backup',
                null,
                InputOption::VALUE_NONE,
                'Do not backup the previously existing file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fetcher = new Fetcher();
        $converter = new Converter($this->resourceDirectory);

        $converter->convertString($fetcher->fetch(), !$input->getOption('no-backup'));

        return 0;
    }
}


class LogfileCommand
{
    protected function configure(): void
    {
        $this
            ->setName('ua-parser:log')
            ->setDescription('Parses the supplied webserver log file.')
            ->addArgument(
                'output',
                InputArgument::REQUIRED,
                'Path to output log file'
            )
            ->addOption(
                'log-file',
                'f',
                InputOption::VALUE_REQUIRED,
                'Path to a webserver log file'
            )
            ->addOption(
                'log-dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Path to webserver log directory'
            )
            ->addOption(
                'include',
                'i',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Include glob expressions for log files in the log directory',
                array('*.log', '*.log*.gz', '*.log*.bz2')
            )
            ->addOption(
                'exclude',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Exclude glob expressions for log files in the log directory',
                array('*error*')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('log-file') && !$input->getOption('log-dir')) {
            throw InvalidArgumentException::oneOfCommandArguments('log-file', 'log-dir');
        }

        $parser = Parser::create();
        $undefinedClients = array();
        /** @var $file SplFileInfo */
        foreach ($this->getFiles($input) as $file) {

            $path = $this->getPath($file);
            $lines = file($path);

            if (empty($lines)) {
                $output->writeln(sprintf('Skipping empty file "%s"', $file->getPathname()));
                $output->writeln('');
                continue;
            }

            $firstLine = reset($lines);

            $reader = AbstractReader::factory($firstLine);
            if (!$reader) {
                $output->writeln(sprintf('Could not find reader for file "%s"', $file->getPathname()));
                $output->writeln('');
                continue;
            }

            $output->writeln('');
            $output->writeln(sprintf('Analyzing "%s"', $file->getPathname()));

            $count = 1;
            $totalCount = count($lines);
            foreach ($lines as $line) {

                try {
                    $userAgentString = $reader->read($line);
                } catch (ReaderException $e) {
                    $count = $this->outputProgress($output, 'E', $count, $totalCount);
                    continue;
                }

                $client = $parser->parse($userAgentString);

                $result = $this->getResult($client);
                if ($result !== '.') {
                    $undefinedClients[] = json_encode(
                        array($client->toString(), $userAgentString),
                        JSON_UNESCAPED_SLASHES
                    );
                }

                $count = $this->outputProgress($output, $result, $count, $totalCount);
            }
            $this->outputProgress($output, '', $count - 1, $totalCount, true);
            $output->writeln('');
        }

        $undefinedClients = $this->filter($undefinedClients);

        $fs = new Filesystem();
        $fs->dumpFile($input->getArgument('output'), implode(PHP_EOL, $undefinedClients));

        return 0;
    }

    private function outputProgress(OutputInterface $output, string $result, int $count, int $totalCount, bool $end = false): int
    {
        if (($count % 70) === 0 || $end) {
            $formatString = '%s  %'.strlen($totalCount).'d / %-'.strlen($totalCount).'d (%3d%%)';
            $result = $end ? str_repeat(' ', 70 - ($count % 70)) : $result;
            $output->writeln(sprintf($formatString, $result, $count, $totalCount, $count / $totalCount * 100));
        } else {
            $output->write($result);
        }

        return $count + 1;
    }

    private function getResult(Client $client): string
    {
        if ($client->device->family === 'Spider') {
            return '.';
        }
        if ($client->ua->family === 'Other') {
            return 'U';
        }
        if ($client->os->family === 'Other') {
            return 'O';
        }
        if ($client->device->family === 'Generic Smartphone') {
            return 'S';
        }
        if ($client->device->family === 'Generic Feature Phone') {
            return 'F';
        }

        return '.';
    }

    private function getFiles(InputInterface $input): Finder
    {
        $finder = Finder::create();

        if ($input->getOption('log-file')) {
            $file = $input->getOption('log-file');
            $finder->append(Finder::create()->in(dirname($file))->name(basename($file)));
        }

        if ($input->getOption('log-dir')) {
            $dirFinder = Finder::create()
                ->in($input->getOption('log-dir'));
            array_map(array($dirFinder, 'name'), $input->getOption('include'));
            array_map(array($dirFinder, 'notName'), $input->getOption('exclude'));

            $finder->append($dirFinder);
        }

        return $finder;
    }

    private function filter(array $lines): array
    {
        return array_values(array_unique($lines));
    }

    private function getPath(SplFileInfo $file): string
    {
        switch ($file->getExtension()) {
            case 'gz':
                $path = 'compress.zlib://'.$file->getPathname();
                break;

            case 'bz2':
                $path = 'compress.bzip2://'.$file->getPathname();
                break;

            default:
                $path = $file->getPathname();
                break;
        }

        return $path;
    }
}

class FetchCommand
{
    /** @var string */
    private $defaultYamlFile;

    public function __construct($defaultYamlFile)
    {
        $this->defaultYamlFile = $defaultYamlFile;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ua-parser:fetch')
            ->setDescription('Fetches an updated YAML file for ua-parser.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'regexes.yaml output file',
                $this->defaultYamlFile
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $fetcher = new Fetcher();
        $fs->dumpFile($input->getArgument('file'), $fetcher->fetch());

        return 0;
    }
}

class ConvertCommand
{
    /** @var string */
    private $resourceDirectory;

    /** @var string */
    private $defaultYamlFile;

    public function __construct($resourceDirectory, $defaultYamlFile)
    {
        $this->resourceDirectory = $resourceDirectory;
        $this->defaultYamlFile = $defaultYamlFile;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ua-parser:convert')
            ->setDescription('Converts an existing regexes.yaml file to a regexes.php file.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to the regexes.yaml file',
                $this->defaultYamlFile
            )
            ->addOption(
                'no-backup',
                null,
                InputOption::VALUE_NONE,
                'Do not backup the previously existing file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getConverter()->convertFile($input->getArgument('file'), $input->getOption('no-backup'));

        return 0;
    }

    private function getConverter(): Converter
    {
        return new Converter($this->resourceDirectory);
    }
}