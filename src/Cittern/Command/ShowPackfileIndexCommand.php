<?php

namespace Cittern\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Cilex\Command\Command;

use Iodophor\Io\FileReader;
use Gittern\Transport\PackfileIndex;

class ShowPackfileIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('packfile:index:show')
            ->setDescription('Shows information about a SHA from the packfile index')
            ->addArgument('file', InputArgument::REQUIRED, 'The packfile index')
            ->addArgument('sha', InputArgument::REQUIRED, 'The SHA to show information about')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new FileReader($input->getArgument('file'));
        $index = new PackfileIndex($reader);

        $sha = $input->getArgument('sha');

        if (!$index->hasSha($sha))
        {
            $output->writeln(sprintf("SHA %s does not exist in this packfile index.", $sha));
        }
        else
        {
//            $output->writeln(sprintf("Index offset: %d", $index->getOffsetForSha($sha)));
            $output->writeln(sprintf("Packfile offset: %d", $index->getPackfileOffsetForSha($sha)));
            $output->writeln(sprintf("CRC: %X", $index->getCrcForSha($sha)));
        }

    }
}