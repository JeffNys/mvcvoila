<?php

namespace Voila\Translate;

use Twig\Compiler;
use Twig\Node\Node;

class TranslateSetNode extends Node
{
  public function __construct(Node $body, int $line, string $tag = null)
  {
    parent::__construct(['body' => $body], [], $line, $tag);
  }

  public function compile(Compiler $compiler)
  {
    $content = $this->getNode('body')->getAttribute('data');
    $content = $content . '1';
    $this->getNode('body')->setAttribute('data', $content);
    // var_dump($content);
    // die;

    $compiler
      ->addDebugInfo($this)
      ->subcompile($this->getNode('body'))
      // ->write('echo "salut";')
    ;
  }
}
