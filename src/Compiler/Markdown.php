<?php

namespace Holkerveen\Pssg\Compiler;

use Exception;
use Holkerveen\Pssg\Compiler\Header\Html as HtmlHeader;
use Holkerveen\Pssg\ServiceProvider\ParserInterface;

class Markdown implements CompilerInterface
{
  /**
   * @var ParserInterface
   */
  private ParserInterface $mdParser;

  public function parse(string $in): string
  {
    $lines = explode("\n", file_get_contents($in));
    $config = $this->parseHeader($lines);

    $html = file_get_contents($config->layout);
    $html = preg_replace_callback('/{{\s*([-.a-zA-Z0-9]+)\s*}}/', function ($matches) use ($config, $lines) {
      if ($matches[1] === 'title') {
        return $config->title;
      }
      if ($matches[1] === 'content') {
        return $this->mdParser->parse(implode("\n", $lines));
      }
      if (strncmp($matches[1], 'vars.', 4) === 0) {
        $key = substr($matches[1], 4);
        if (isset($config->vars[$key])) {
          return $config->vars[$key];
        }
      }

      throw new Exception("Undefined key '{$matches[1]}'");
    }, $html);

    return $html;
  }

  /**
   * @param string[] $lines
   * @return array
   */
  private function parseHeader(array &$lines): HtmlHeader
  {
    $lineNumber = 0;
    if (rtrim($lines[$lineNumber]) !== '---') {
      return new HtmlHeader;
    }

    $endHeader = false;
    $yaml = [];
    while (isset($lines[++$lineNumber])) {
      if ($lines[$lineNumber] === '---') {
        $endHeader = true;
        break;
      }
      $yaml[] = $lines[$lineNumber];
    }

    if (!$endHeader) return new HtmlHeader;

    $lines = array_slice($lines, $lineNumber + 1);
    return new HtmlHeader(spyc_load_file(implode("\n", $yaml)));
  }

  public function getInputExtension(): string
  {
    return "md";
  }

  public function getOutputExtension(): string
  {
    return "html";
  }

  public function setMarkdownParser(ParserInterface $mdParser)
  {
    $this->mdParser = $mdParser;
  }
}
