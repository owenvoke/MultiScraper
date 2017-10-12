<?php

use YeTii\General\Str;

/**
 * Format an integer to a bytes string
 *
 * @param int $bytes
 * @return string
 */
function format_bytes($bytes)
{
    if (!$bytes) return "0.00 B";
    $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $e = floor(log($bytes, 1024));
    return round($bytes/pow(1024, $e), 2).$s[$e];
}

/**
 * Format files array into nested directory array tree
 *
 * @param array $files
 * @return string
 */
function nest_files(array $files) {
    $return = [];
    foreach ($files as $file) {
        $tmp =& $return;
        foreach (explode('/', Str::beforeLast($file->path, '/')) as $p) {
            if (!$p) continue;
            if (!isset($tmp[$p]))
                $tmp[$p] = [];
            $tmp =& $tmp[$p];
        }
        $tmp[Str::afterLast($file->path, '/')] = $file;
    }
    return $return;
}

/**
 * Print, and then optionally die
 *
 * @param mixed $var
 * @param bool  $die
 */
function printDie($var, $die = true)
{
    $var = print_r($var, true);
    if (preg_match('/<[^<>]+>/', $var)) {
        $var = htmlspecialchars($var);
    }
    print '<pre>' . $var . '</pre>';
    if ($die) {
        die();
    }
}

/**
 * Convert a string to bytes
 *
 * @param string $str
 * @return int|null
 */
function strtobytes($str)
{
    preg_match('/(\d+(?:\.\d+|))(?:&nbsp;|\W)*(b|k|m|g|t|p)/i', $str, $m);
    if (!isset($m[1])) {
        return null;
    }
    if (!isset($m[2]) && isset($m[1])) {
        return $m[1];
    }
    $multiplier = 1;
    switch (strtolower($m[2])) {
        case 'p':
            $multiplier *= 1024;
        case 't':
            $multiplier *= 1024;
        case 'g':
            $multiplier *= 1024;
        case 'm':
            $multiplier *= 1024;
        case 'k':
            $multiplier *= 1024;
    }

    return (int)($multiplier * $m[1]);
}

/**
 * Check if an object is an attribute
 *
 * @param object $obj
 * @return bool
 */
function is_attribute($obj)
{
    if (!is_object($obj)) {
        return false;
    }
    $class = get_class($obj);

    return Str::startsWith($class, 'YeTii\\MultiScraper\\Attributes\\');
}

/**
 * Crawl data for an attribute
 *
 * @param mixed $obj
 * @return array|object
 */
function crawl_attribute($obj)
{
    if (is_attribute($obj)) {
        return crawl_attribute($obj->get());
    } elseif (is_array($obj) || is_object($obj)) {
        $arr = [];
        foreach ($obj as $key => $value) {
            $arr[$key] = crawl_attribute($value);
        }

        return is_object($obj) ? (object)$arr : $arr;
    } else {
        return $obj;
    }
}

/**
 * Get information from a torrent file
 *
 * @param string $input
 * @return array|bool|null|string
 * @throws Exception
 */
function torrent_info($input)
{
    if (file_exists($input) || preg_match('/^https??:\/\//', $input)) {
        $str = file_get_contents($input);
    } else {
        throw new \Exception("Torrent Not Found: `{$input}`", 1);
    }

    $bencode = parse_torrent($str);
    if (isset($bencode['info']['pieces'])) {
        unset($bencode['info']['pieces']);
    }

    return isset($bencode['info_hash']) ? $bencode : null;
}

/**
 * Parse a torrent file using the data string
 *
 * @param mixed $s
 * @return array|bool|string
 */
function parse_torrent($s)
{
    static $str;
    $str = $s;

    if ($str{0} == 'd') {
        $str = substr($str, 1);
        $ret = [];
        while (strlen($str) && $str{0} != 'e') {
            $key = parse_torrent($str);
            if (strlen($str) == strlen($s)) {
                break;
            } // prevent endless cycle if no changes made
            if (!strcmp($key, "info")) {
                $save = $str;
            }
            //          echo ".",$str{0};
            $value = parse_torrent($str);
            if (!strcmp($key, "info")) {
                $tosha = substr($save, 0, strlen($save) - strlen($str));
                $ret['info_hash'] = sha1($tosha);
            }

            // process hashes - make this stuff an array by piece
            if (!strcmp($key, "pieces")) {
                $value = explode("====",
                    substr(
                        chunk_split($value, 20, "===="),
                        0, -4
                    )
                );
            };
            $ret[$key] = $value;
        }
        $str = substr($str, 1);

        return $ret;
    } else {
        if ($str{0} == 'i') {
            //       echo "_";
            $ret = substr($str, 1, strpos($str, "e") - 1);
            $str = substr($str, strpos($str, "e") + 1);

            return $ret;
        } else {
            if ($str{0} == 'l') {
                //       echo "#";
                $ret = [];
                $str = substr($str, 1);
                while (strlen($str) && $str{0} != 'e') {
                    $value = parse_torrent($str);
                    if (strlen($str) == strlen($s)) {
                        break;
                    } // prevent endless cycle if no changes made
                    $ret[] = $value;
                }
                $str = substr($str, 1);

                return $ret;
            } else {
                if (is_numeric($str{0})) {
                    //       echo "@";
                    $namelen = substr($str, 0, strpos($str, ":"));
                    $name = substr($str, strpos($str, ":") + 1, $namelen);
                    $str = substr($str, strpos($str, ":") + 1 + $namelen);

                    return $name;
                }
            }
        }
    }
}
