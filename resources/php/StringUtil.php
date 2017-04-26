<?php

class StringUtil
{
    public static function isDuplication($str)
    {
        $chars = str_split($str);
        for ($i = 0, $count_i = count($chars); $i < $count_i;) {
            unset($chars[$i]);

            ++$i;
            for ($j = 0, $count_j = count($chars); $j < $count_j; $j++) {
                if ($chars[$i] === $chars[$i + $j]) return true;
            }
        }
        return false;
    }

}

echo StringUtil::isDuplication($argv[1]) ? 'duplication!' : 'not duplication';
echo PHP_EOL;
