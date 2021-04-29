<?php

namespace App\DQL;

use DateTimeInterface;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * DateAddFunction ::= "DATEADD" "("
 */
class DateAddFunction extends FunctionNode
{
    public ?string $datePart;
    public ?Node $number;
    public ?Node $date;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'DATEADD(' . $this->datePart . ', ' . $this->number->dispatch($sqlWalker) . ', ' . $this->date->dispatch($sqlWalker) . ')';
    }

    /**
     * @param Parser $parser
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->datePart = $lexer->token['value'];

//        $this->datePart = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->number = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
