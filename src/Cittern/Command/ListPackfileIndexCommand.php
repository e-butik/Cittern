<?php

namespace Cittern\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Cilex\Command\Command;

use Iodophor\Io\FileReader;
use Gittern\Transport\PackfileIndex;

class ListPackfileIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('packfile:index:list')
            ->setDescription('Lists various information in the packfile index')
            ->addArgument('file', InputArgument::REQUIRED, 'The packfile index')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new FileReader($input->getArgument('file'));
        $index = new PackfileIndex($reader);

        $this->writePaddedBlock($output, "Fanout table", 'bg=blue;fg=white');

        foreach (range(0, 255) as $prefix)
        {
            $output->writeln(sprintf("%02X: %d", $prefix, $index->readFanoutForPrefix($prefix)));
        }

        $this->writePaddedBlock($output, "Sha table", 'bg=blue;fg=white');

        foreach ($index->getShas() as $offset => $sha)
        {
            $output->writeln(sprintf("%d: %s", $offset, $sha));
        }
    }

    protected function writePaddedBlock(OutputInterface $output, $text, $color)
    {
        $formatter = $this->getHelperSet()->get('formatter');

        $output->writeln(array(
            '',
            $formatter->formatBlock($text, $color, true),
            '',
        ));
    }
}