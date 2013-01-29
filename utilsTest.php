<?php

class utilsTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }
    
    /**
     * escapeQuery() のテスト
     */
    public function testEscapeQuery()
    {
        // 空白
        $value = '';
        $check = '';
        $result = SolrUtil::escapeQuery($value);
        $this->assertSame($check, $result);
        
        // \ をエスケープ
        $value = '\\';
        $check = '\\\\';
        $result = SolrUtil::escapeQuery($value);
        $this->assertSame($check, $result);
        
        // " をエスケープ
        $value = '"';
        $check = '\\"';
        $result = SolrUtil::escapeQuery($value);
        $this->assertSame($check, $result);
        
        // \ と " をエスケープ
        $value = '\\あ\\\\い""う\\"え"\\お"';
        $check = '\\\\あ\\\\\\\\い\\"\\"う\\\\\\"え\\"\\\\お\\"';
        $result = SolrUtil::escapeQuery($value);
        $this->assertSame($check, $result);
    }
    
    /**
     * parseSearchPhrase2SolrQuery() のテスト
     */
    public function testParseSearchPhrase2SolrQuery()
    {
        $search_words = '';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('', $result);
        
        $search_words = 'A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A"', $result);
        
        $search_words = ' A B   C ';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "B" "C"', $result);
        
        $search_words = ' "A B" C';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A B" "C"', $result);
        
        $search_words = '　A 　B　';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "B"', $result);
        
        $search_words = ' "A　 B"　 C';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A B" "C"', $result);
        
        $search_words = '"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"\""', $result);
        
        $search_words = '"" " "';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('', $result);
        
        $search_words = ' "A B" C"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A B" "C\""', $result);
        
        $search_words = 'A OR B OR C';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B" OR "C"', $result);
        
        $search_words = 'A OR B C OR D';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B" "C" OR "D"', $result);
        
        $search_words = '(A OR B) (C OR D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A" OR "B") ("C" OR "D")', $result);
        
        $search_words = '"(A OR B)" (C OR D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"(A OR B)" ("C" OR "D")', $result);
        
        $search_words = '(A OR "B) (C" OR D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A" OR "B) (C" OR "D")', $result);
        
        $search_words = 'A OR "B OR C" OR D';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B OR C" OR "D"', $result);
        
        $search_words = 'OR';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"OR"', $result);
        
        $search_words = 'OR A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"OR" "A"', $result);
        
        $search_words = 'A OR';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "OR"', $result);
        
        $search_words = 'OR OR OR OR';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"OR" OR "OR" "OR"', $result);
        
        $search_words = 'A | B | C';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B" OR "C"', $result);
        
        $search_words = 'A | B C | D';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B" "C" OR "D"', $result);
        
        $search_words = '(A | B) (C | D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A" OR "B") ("C" OR "D")', $result);
        
        $search_words = '"(A | B)" (C | D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"(A | B)" ("C" OR "D")', $result);
        
        $search_words = '(A | "B) (C" | D)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A" OR "B) (C" OR "D")', $result);
        
        $search_words = 'A | "B | C" | D';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" OR "B | C" OR "D"', $result);
        
        $search_words = '|';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"|"', $result);
        
        $search_words = '| A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"|" "A"', $result);
        
        $search_words = 'A |';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "|"', $result);
        
        $search_words = '| | | |';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"|" OR "|" "|"', $result);
        
        $search_words = '(';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"("', $result);
        
        $search_words = ')';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('")"', $result);
        
        $search_words = '(()';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"("', $result);
        
        $search_words = '())';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('")"', $result);
        
        $search_words = '()';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('', $result);
        
        $search_words = '("")';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('', $result);
        
        $search_words = '( A )';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A")', $result);
        
        $search_words = '(OR)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("OR")', $result);
        
        $search_words = '(NOT)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("NOT")', $result);
        
        $search_words = '("A")';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A")', $result);
        
        $search_words = '(")';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("\"")', $result);
        
        $search_words = '(-)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("-")', $result);
        
        $search_words = '(-A)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('(NOT "A")', $result);
        
        $search_words = '-';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"-"', $result);
        
        $search_words = '-A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "A"', $result);
        
        $search_words = '--';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "-"', $result);
        
        $search_words = '-"A B"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "A B"', $result);
        
        $search_words = '-A | -B';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "A" OR NOT "B"', $result);
        
        $search_words = 'NOT A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"NOT" "A"', $result);
        
        $search_words = 'NOT';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"NOT"', $result);
        
        $search_words = 'NOT A';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"NOT" "A"', $result);
        
        $search_words = '-" "';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"-"', $result);
        
        $search_words = '-""';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"-"', $result);
        
        $search_words = '-"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "\""', $result);
        
        $search_words = '- ()';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"-"', $result);
        
        $search_words = '-()';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('', $result);
        
        $search_words = '-(';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "("', $result);
        
        $search_words = '-)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT ")"', $result);
        
        $search_words = '-(';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "("', $result);
        
        $search_words = '(A OR B)-C';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('("A" OR "B") NOT "C"', $result);
        
        $search_words = '"A"B';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "B"', $result);
        
        $search_words = '"-A"-B';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"-A" NOT "B"', $result);
        
        $search_words = '-A(B OR C)';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "A" ("B" OR "C")', $result);
        
        $search_words = 'A"B"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "B"', $result);
        
        $search_words = '-A"-B"';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('NOT "A" "-B"', $result);
        
        $search_words = '"""""';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"\""', $result);
        
        $search_words = '"A""';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"A" "\""', $result);
        
        $search_words = 'OR "A -( B(C OR -D))E OR NOT F | OR G H(I -J) NOT';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"OR" "\"A" NOT ("B" ("C" OR NOT "D")) "E" OR "NOT" "F" OR "OR" "G" "H" ("I" NOT "J") "NOT"', $result);
        
        $search_words = 'OR "A"-"(" "B"(C   OR -D)"E OR NOT  F"| OR "G H" ( I -J ) "-" "" - NOT L(MN) | ';
        $result = SolrUtil::getSearchPhrase2SolrQuery($search_words);
        $this->assertSame('"OR" "A" NOT "(" "B" ("C" OR NOT "D") "E OR NOT F" OR "OR" "G H" ("I" NOT "J") "-" "-" "NOT" "L" ("MN") "|"', $result);
        
    }
}

