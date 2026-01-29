<?php namespace Admin\Services\Form;

use Admin\Core\Registry;
/**
 * 
**/
trait ValidationRules
{
    public function required($str)
    {
        return is_array($str) ? (empty($str) === false) : (trim((string)$str) !== '');
    }

    public function regex_match($str, $regex)
    {
        return (bool) preg_match((string)$regex, (string)$str);
    }

    public function matches($str, $field)
    {
        if (isset($this->parent) && ($otherField = $this->parent->getField($field))) {
            return ($str === $this->parent->get_value($field));
        }
        return (isset($_POST[$field]) && $str === $_POST[$field]);
    }

    public function differs($str, $field)
    {
        return ! $this->matches($str, $field);
    }

    public function min_length($str, $val)
    {
        if ( ! is_numeric($val)) return false;
        return ($val <= mb_strlen((string)$str));
    }

    public function max_length($str, $val)
    {
        if ( ! is_numeric($val)) return false;
        return ($val >= mb_strlen((string)$str));
    }

    public function exact_length($str, $val)
    {
        if ( ! is_numeric($val)) return false;
        return (mb_strlen((string)$str) === (int)$val);
    }

    public function valid_url($str)
    {
        if (empty($str)) return false;
        elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', (string)$str, $matches)) {
            if (empty($matches[2])) return false;
            elseif ( ! in_array(strtolower($matches[1]), ['http', 'https'], true)) return false;
            $str = $matches[2];
        }
        if (ctype_digit((string)$str)) return false;
        if (preg_match('/^\[([^\]]+)\]/', (string)$str, $matches) && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $str = 'ipv6.host'.substr((string)$str, strlen($matches[1]) + 2);
        }
        return (filter_var('http://'.$str, FILTER_VALIDATE_URL) !== false);
    }

    public function valid_email($str)
    {
        if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', (string)$str, $matches)) {
            $domain = defined('INTL_IDNA_VARIANT_UTS46') ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46) : idn_to_ascii($matches[2]);
            if ($domain !== false) $str = $matches[1].'@'.$domain;
        }
        return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public function alpha($str)
    {
        return ctype_alpha((string)$str);
    }

    public function alpha_numeric($str)
    {
        return ctype_alnum((string)$str);
    }

    public function alpha_numeric_spaces($str)
    {
        return (bool) preg_match('/^[A-Z0-9 ]+$/i', (string)$str);
    }

    public function alpha_dash($str)
    {
        return (bool) preg_match('/^[a-z0-9_-]+$/i', (string)$str);
    }

    public function numeric($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', (string)$str);
    }

    public function integer($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+$/', (string)$str);
    }

    public function decimal($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', (string)$str);
    }

    public function greater_than($str, $min)
    {
        return is_numeric($str) ? ((float)$str > (float)$min) : false;
    }

    public function greater_than_equal_to($str, $min)
    {
        return is_numeric($str) ? ((float)$str >= (float)$min) : false;
    }

    public function less_than($str, $max)
    {
        return is_numeric($str) ? ((float)$str < (float)$max) : false;
    }

    public function less_than_equal_to($str, $max)
    {
        return is_numeric($str) ? ((float)$str <= (float)$max) : false;
    }

    public function in_list($value, $list)
    {
        return in_array($value, explode(',', (string)$list), true);
    }

    public function is_natural($str)
    {
        return ctype_digit((string)$str);
    }

    public function is_natural_no_zero($str)
    {
        return ($str != 0 && ctype_digit((string)$str));
    }

    public function is_unique($str, $field)
    {
        sscanf((string)$field, '%[^.].%[^.]', $table, $column);
        $db = Registry::getInstance('Database');
        return isset($db)
            ? ($db->limit(1)->get_where((string)$table, [$column => $str])->num_rows() === 0)
            : false;
    }

    public function valid_base64($str)
    {
        return (base64_encode(base64_decode((string)$str)) === (string)$str);
    }
}
