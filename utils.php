<?php 

class utils
{
    public static function escapeQuery($value)
    {
        $value = str_replace("\\", "\\\\", $value);
        $value = str_replace('"', '\"', $value);
        
        return $value;
    }
    
    /**
     * 文字列を
     *  キーワード(AND),フレーズ,ORキーワード,NOTキーワード にパースする。
     * 
     * @param string $search_phrase
     * @return string 
     */
    public static function parseSearchPhrase2SolrQuery($search_phrase)
    {
        $result = '';
        
        $search_phrase = mb_convert_kana($search_phrase, 's', Utility::ENCODING);
        
        $search_phrase = trim($search_phrase);
        if (strlen($search_phrase) === 0) {
            return $result;
        }
        $search_phrase = preg_replace('/ {2,}/', ' ', trim($search_phrase));
        
        if (preg_match_all('/"([^"]*)"/', $search_phrase, $matches, PREG_OFFSET_CAPTURE) === false) {
            return $result;
        }
        
        $parts = array();
        $offset = 0;
        $cnt = count($matches[1]);
        for ($i = 0; $i <= $cnt; $i++) {
            if (isset($matches[1][$i])) {
                $tmp = substr($search_phrase, $offset, $matches[1][$i][1] - 1 - $offset);
            } else {
                $tmp = substr($search_phrase, $offset);
            }
            if (strlen($tmp) > 0) {
                $parts = array_merge($parts, explode(' ', str_replace(array('(', ')'), array(' "(" ', ' ")" '), preg_replace(array('/-([^ ])/', '/-$/'), array('"-"$1', '"-"'), $tmp))));
            }
            if (isset($matches[1][$i])) {
                $parts[] = trim($matches[1][$i][0]);
                $offset = $matches[1][$i][1] + strlen($matches[1][$i][0]) + 1;
            }
        }
        
        $preor = false;
        $or    = false;
        $parentheses = array();
        foreach ($parts as $key => $part) {
            $preor = $or;
            if ($or && ($part == 'OR' || $part == '|') && isset($parts[$key + 1])) {
                $result .= 'OR ';
                $or = false;
            } elseif ($part === '"-"') {
                if (isset($parts[$key + 1]) && $parts[$key + 1] !== '' && !($parts[$key + 1] === '")"' && count($parentheses) > 0)) {
                    $result .= 'NOT ';
                    $or = false;
                } else {
                    $result .= '"-" ';
                    $or = true;
                }
            } elseif (strpos($part, '"-"') === 0) {
                $result .= str_replace('NOT ', 'NOT "', self::escapeQuery(str_replace('"-"', '-', substr_replace($part, 'NOT ', 0, 3)))) . '" ';
                $or = true;
            } elseif ($part === '"("') {
                $result .= ' (';
                $parentheses[] = strlen($result) - 1;
                $or = false;
            } elseif ($part === '")"') {
                if (count($parentheses) > 0) {
                    if ($result[strlen($result) - 1] === '(') {
                        $result = substr($result, 0, -1);
                        if (substr($result, -5) === 'NOT  ') {
                            $result = substr($result, 0, -5);
                        }
                        $or = $preor;
                    } else {
                        $result = rtrim($result) . ') ';
                        $or = true;
                    }
                    array_pop($parentheses);
                } else {
                    $result .= ' ")" ';
                    $or = true;
                }
            } elseif ($part === '') {
            } else {
                $result .= '"' . self::escapeQuery($part) . '" ';
                $or = true;
            }
        }
        
        if (count($parentheses) > 0) {
            $parentheses = array_reverse($parentheses);
            foreach ($parentheses as $value) {
                $result = substr_replace($result, ' "(" ', $value, 1);
            }
        }

        $result = preg_replace('/ {2,}/', ' ', trim($result));
        
        return $result;
    }
}

