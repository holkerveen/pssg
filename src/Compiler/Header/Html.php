<?php

namespace Holkerveen\Pssg\Compiler\Header;

class Html
{
  public string $layout;
  public string $title;
  public array $vars;

  public function __construct(array $config = [])
  {
    $this->layout = $config['layout'] ?? 'default/layout.html';
    unset($config['layout']);

    $this->title = $config['title'] ?? '';
    unset($config['title']);

    $this->vars = $config['vars'] ?? [];
    unset($config['vars']);
  }
}
