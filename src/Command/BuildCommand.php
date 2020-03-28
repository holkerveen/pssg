<?php

namespace Holkerveen\Pssg\Command;

use Exception;
use Holkerveen\Pssg\HasContainerInterface;
use Holkerveen\Pssg\Compiler;
use Holkerveen\Pssg\HasContainerTrait;
use Holkerveen\Pssg\Output\FileOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command implements HasContainerInterface
{
  use HasContainerTrait;
  protected static $defaultName = 'build';

  protected function configure()
  {
    $this
      ->setDescription("Build site files")
      ->setHelp(<<<EOF
        This command wil build all your assets.
        EOF
      );

    $this->addArgument('path', InputArgument::OPTIONAL, 'Path to your project', '.');
    $this->addOption('output','o',InputOption::VALUE_REQUIRED, 'Output path [dist/]');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @throws Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $compiler = new Compiler($input->getArgument('path'));
    $compiler->setCompilers([
      $this->container->get('compiler.markdown'),
      $this->container->get('compiler.scss'),
      $this->container->get('compiler.typescript'),
    ]);
    $compiler->setOutput(new FileOutput($input->getOption('output') ?? 'dist'));
    $compiler->build();
    return 0;
  }
}
