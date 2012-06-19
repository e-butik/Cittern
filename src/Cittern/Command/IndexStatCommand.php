<?php

namespace Cittern\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Cilex\Command\Command;

use Gittern\Repository;
use Gittern\Transport\NativeTransport;
use Gittern\Configurator;

class IndexStatCommand extends Command
{
    const STAT_FILE_TYPE_S_IFMT = 0170000;
    const STAT_FILE_TYPE_S_IFIFO = 0010000;
    const STAT_FILE_TYPE_S_IFCHR = 0020000;
    const STAT_FILE_TYPE_S_IFDIR = 0040000;
    const STAT_FILE_TYPE_S_IFBLK = 0060000;
    const STAT_FILE_TYPE_S_IFREG = 0100000;
    const STAT_FILE_TYPE_S_IFLNK = 0120000;
    const STAT_FILE_TYPE_S_IFSOCK = 0140000;

    protected function configure()
    {
        $this
            ->setName('index:stat')
            ->setDescription('Show stat() info from the index')
            ->addArgument('file', InputArgument::OPTIONAL, 'The file to stat()')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'If set, will stat all files');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transport = new NativeTransport(getcwd().'/.git');

        $repo = new Repository();
        $repo->setTransport($transport);

        $configurator = new Configurator;
        $configurator->defaultConfigure($repo);

        $index = $repo->getIndex();
        $entry = $index->getEntryNamed($input->getArgument('file'));

        $output->write('File type:                 ');
        switch ($entry->getMode() & self::STAT_FILE_TYPE_S_IFMT) {
          case self::STAT_FILE_TYPE_S_IFBLK:  $output->write("block device\n");            break;
          case self::STAT_FILE_TYPE_S_IFCHR:  $output->write("character device\n");        break;
          case self::STAT_FILE_TYPE_S_IFDIR:  $output->write("directory\n");               break;
          case self::STAT_FILE_TYPE_S_IFIFO:  $output->write("FIFO/pipe\n");               break;
          case self::STAT_FILE_TYPE_S_IFLNK:  $output->write("symlink\n");                 break;
          case self::STAT_FILE_TYPE_S_IFREG:  $output->write("regular file\n");            break;
          case self::STAT_FILE_TYPE_S_IFSOCK: $output->write("socket\n");                  break;
          default:                            $output->write("unknown?\n");                break;
        }

        $output->writeln('I-node number:             '.$entry->getInode());
        $output->writeln('Mode:                      '.decoct($entry->getMode()).' (octal)');
        $output->writeln('Ownership:                 UID='.$entry->getUid().'   GID='.$entry->getGid());
        $output->writeln('File size:                 '.$entry->getFilesize().' bytes');
        $output->writeln('Last status change:        '.date('r', $entry->getCtime()));
        $output->writeln('Last file modification:    '.date('r', $entry->getMtime()));
    }
}