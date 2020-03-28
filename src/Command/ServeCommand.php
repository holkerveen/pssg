<?php

namespace Holkerveen\Pssg\Command;

use Exception;
use Holkerveen\Pssg\HasContainerInterface;
use Holkerveen\Pssg\Compiler;
use Holkerveen\Pssg\HasContainerTrait;
use Holkerveen\Pssg\Output\FileOutput;
use Holkerveen\Pssg\Server\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command implements HasContainerInterface
{
  use HasContainerTrait;

  protected static $defaultName = 'serve';

    protected function configure()
  {
    $this
      ->setDescription("Start development server")
      ->setHelp(<<<EOF
        This command wil start a development server to help you write your site. It will
        compile your templates to memory and serve them. Your source files will be
        recompiled on change.
        EOF
      );

    $this
      ->addArgument('path', InputArgument::OPTIONAL, 'Path to your project', '.')
      ->addOption('host', null, InputOption::VALUE_REQUIRED, "Host ip to bind to", "127.0.0.1");
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
    $compiler->setOutput(new FileOutput("/tmp/pssg/serve"));
    $compiler->build();

    $compiler->watch();
    (new Server)->serve($compiler->getOutput());

    return 0;
  }
}
