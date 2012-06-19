<?php

namespace Cittern;

use Cilex\Application as CilexApplication;
use Symfony\Component\Finder\Finder;

class Application extends CilexApplication
{
  public function __construct()
  {
    parent::__construct('Cittern', "0.1");

    $finder = new Finder();

    $finder->files()->in(__DIR__.'/Command')->name('*.php');

    foreach ($finder as $file) {
      $relative_class_name = str_replace(array('.php', '/'), array('', '\\'), $file->getRelativePathname());
      $class_name = 'Cittern\\Command\\'.$relative_class_name;

      $this->command(new $class_name);
    }
  }
}