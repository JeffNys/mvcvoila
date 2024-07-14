<?php

namespace Voila\Translate;

use Twig\Token;
use Voila\Translate\TranslateSetNode;
use Twig\TokenParser\AbstractTokenParser;

class TranslateTokenParser extends AbstractTokenParser
{
  public function parse(Token $token): TranslateSetNode
  {
    $parser = $this->parser;
    $stream = $parser->getStream();
    $lineNbr = $token->getLine();
    // $name = $stream->expect(Token::NAME_TYPE)->getValue();
    // $stream->expect(Token::OPERATOR_TYPE, '=');
    // $value = $parser->getExpressionParser()->parseExpression();
    $stream->expect(Token::BLOCK_END_TYPE);
    $body = $parser->subparse([$this, 'decideTransEnd'], dropNeedle: true);
    $stream->expect(Token::BLOCK_END_TYPE);


    return new TranslateSetNode(
      // name: $name,
      body: $body,
      line: $lineNbr,
      tag: $this->getTag(),
    );
  }

  public function getTag(): string
  {
    return 'trans';
  }

  public function decideTransEnd(Token $token): bool
  {
    return $token->test('endtrans');
  }
}
