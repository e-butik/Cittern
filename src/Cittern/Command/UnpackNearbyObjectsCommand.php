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

use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\Node\TreeNode;

class UnpackNearbyObjectsCommand extends Command
{
    protected $candidates = array();

    protected function configure()
    {
        $this
            ->setName('repo:unpack-nearby')
            ->setDescription('Unpack nearby objects from packfiles')
            ->addArgument('commitish', InputArgument::REQUIRED, 'The base')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transport = new NativeTransport(getcwd().'/.git');

        $repo = new Repository();
        $repo->setTransport($transport);

        $configurator = new Configurator;
        $configurator->defaultConfigure($repo);

        $commit = $repo->getObject($input->getArgument('commitish'));

        if (!$commit)
        {
            throw new \RuntimeException("The base doesn't exist");
        }

        $this->candidates[] = $commit;

        $this->processTree($commit->getTree());

        foreach ($this->candidates as $candidate)
        {
            $sha = $candidate->getSha();
            if (!$transport->isLoose($sha))
            {
                $transport->putRawObject($transport->fetchRawObject($sha));
            }
        }
    }

    protected function processTree(Tree $tree)
    {
        $this->candidates[] = $tree;
        foreach ($tree as $node)
        {
            if ($node instanceOf TreeNode)
            {
                $this->processTree($node->getTree());
            }
            else
            {
                $this->candidates[] = $node->getRelatedObject();
            }
        }
    }
}