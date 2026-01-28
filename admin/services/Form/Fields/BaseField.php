<?php namespace Admin\Services\Form\Fields;
/**
 * 
**/
use \Admin\Services\Form\ValidationRules;
/**
 * 
**/
abstract class BaseField
{
    use ValidationRules;

    protected $CI;
    protected $data;
    protected $value;
    protected $extra;
    protected $rules = [];
    protected $label;
    protected $error;
    protected $parent;
    
    protected static $error_delimiters = ['<p>', '</p>'];

    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function __construct($data = '', $value = '', $extra = '')
    {
        $this->CI =& get_instance();
        $this->data = $data;
        $this->value = $value;
        $this->extra = $extra;
        
        if (is_array($data)) {
            $this->label = isset($data['label']) ? $data['label'] : (isset($data['name']) ? $data['name'] : '');
        } else {
            $this->label = (string)$data;
        }
    }

    public function setRules($rules)
    {
        if (is_string($rules)) {
            $rules = preg_split('/\|(?![^\[]*\])/', $rules);
        }
        $this->rules = (array)$rules;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function validate($posted_value)
    {
        foreach ($this->rules as $rule) {
            $param = FALSE;
            if (preg_match('/(.*?)\[(.*)\]/', (string)$rule, $match)) {
                $rule = $match[1];
                $param = $match[2];
            }

            if (method_exists($this, (string)$rule)) {
                $result = $param !== FALSE ? $this->$rule($posted_value, $param) : $this->$rule($posted_value);
                if ($result === FALSE) {
                    $this->error = $this->_get_error_message((string)$rule, $param);
                    return false;
                }
            } elseif (function_exists((string)$rule)) {
                $result = $param !== FALSE ? $rule($posted_value, $param) : $rule($posted_value);
                if ($result === FALSE) {
                     $this->error = $this->_get_error_message((string)$rule, $param);
                     return false;
                }
            }
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function _get_error_message($rule, $param = FALSE)
    {
        // Simple error message logic, can be improved to use lang files
        $label = $this->label ?: 'Field';
        return "The $label field failed validation: $rule" . ($param !== FALSE ? " ($param)" : "");
    }

    abstract public function render();

    protected function _attributes_to_string($attributes)
    {
        if (empty($attributes)) return '';
        if (is_object($attributes)) $attributes = (array)$attributes;
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' '.(string)$key.'="'.(string)$val.'"';
            }
            return $atts;
        }
        return is_string($attributes) ? ' '.$attributes : '';
    }

    protected function _parse_form_attributes($attributes, $default)
    {
        if (is_array($attributes)) {
            foreach ($default as $key => $val) {
                if (isset($attributes[$key])) {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }
            if (count($attributes) > 0) {
                $default = array_merge($default, $attributes);
            }
        }
        $att = '';
        foreach ($default as $key => $val) {
            if ($key === 'value') {
                $val = html_escape((string)$val);
            } elseif ($key === 'name' && ! strlen((string)$default['name'])) {
                continue;
            }
            $att .= (string)$key.'="'.(string)$val.'" ';
        }
        return $att;
    }
}
