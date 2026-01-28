<?php namespace Admin\Services\Form;
/**
 * 
**/
class Form
{
    use ValidationRules;

    protected $CI;
    protected $fields = [];
    protected $error_array = [];
    protected $error_prefix = '<p>';
    protected $error_suffix = '</p>';

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Form Declaration
     *
     * @param	string	$action		the URI we're targeting
     * @param	array	$attributes	HTML attributes
     * @param	array	$hidden		array of hidden fields
     * @return	string
     */
    public function open($action = '', $attributes = [], $hidden = [])
    {
        if ( ! $action)
        {
            $action = $this->CI->config->site_url($this->CI->uri->uri_string());
        }
        elseif (strpos((string)$action, '://') === FALSE)
        {
            $action = $this->CI->config->site_url((string)$action);
        }

        $attributes = $this->_attributes_to_string($attributes);

        if (stripos((string)$attributes, 'method=') === FALSE)
        {
            $attributes .= ' method="post"';
        }

        if (stripos((string)$attributes, 'accept-charset=') === FALSE)
        {
            $attributes .= ' accept-charset="'.strtolower((string)config_item('charset')).'"';
        }

        $form = '<form action="'.$action.'"'.$attributes.">\n";

        if (is_array($hidden))
        {
            foreach ($hidden as $name => $value)
            {
                $form .= '<input type="hidden" name="'.(string)$name.'" value="'.html_escape((string)$value).'" />'."\n";
            }
        }

        if ($this->CI->config->item('csrf_protection') === TRUE && strpos((string)$action, (string)$this->CI->config->base_url()) !== FALSE && ! stripos((string)$form, 'method="get"'))
        {
            $noise = random_bytes(1);
            list(, $noise) = unpack('c', $noise);

            $prepend = $append = '';
            if ($noise < 0)
            {
                $prepend = str_repeat(" ", abs($noise));
            }
            elseif ($noise > 0)
            {
                $append  = str_repeat(" ", $noise);
            }

            $form .= sprintf(
                '%s<input type="hidden" name="%s" value="%s" />%s%s',
                $prepend,
                $this->CI->security->get_csrf_token_name(),
                $this->CI->security->get_csrf_hash(),
                $append,
                "\n"
            );
        }

        return $form;
    }

    /**
     * Form Declaration - Multipart type
     *
     * @param	string	$action		the URI we're targeting
     * @param	array	$attributes	HTML attributes
     * @param	array	$hidden		array of hidden fields
     * @return	string
     */
    public function open_multipart($action = '', $attributes = [], $hidden = [])
    {
        if (is_string($attributes))
        {
            $attributes .= ' enctype="multipart/form-data"';
        }
        else
        {
            $attributes['enctype'] = 'multipart/form-data';
        }

        return $this->open($action, $attributes, $hidden);
    }

    /**
     * Form Close Tag
     *
     * @param	string	$extra
     * @return	string
     */
    public function close($extra = '')
    {
        return '</form>'.$extra;
    }

    /**
     * Attributes To String
     *
     * @param	mixed	$attributes
     * @return	string
     */
    protected function _attributes_to_string($attributes)
    {
        if (empty($attributes))
        {
            return '';
        }

        if (is_object($attributes))
        {
            $attributes = (array) $attributes;
        }

        if (is_array($attributes))
        {
            $atts = '';
            foreach ($attributes as $key => $val)
            {
                $atts .= ' '.(string)$key.'="'.(string)$val.'"';
            }

            return $atts;
        }

        if (is_string($attributes))
        {
            return ' '.$attributes;
        }

        return '';
    }

    /**
     * Parse Form Attributes
     *
     * @param	array	$attributes
     * @param	array	$default
     * @return	string
     */
    public function parse_form_attributes($attributes, $default)
    {
        if (is_array($attributes))
        {
            foreach ($default as $key => $val)
            {
                if (isset($attributes[$key]))
                {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }

            if (count($attributes) > 0)
            {
                $default = array_merge($default, $attributes);
            }
        }

        $att = '';

        foreach ($default as $key => $val)
        {
            if ($key === 'value')
            {
                $val = html_escape((string)$val);
            }
            elseif ($key === 'name' && ! strlen((string)$default['name']))
            {
                continue;
            }

            $att .= (string)$key.'="'.(string)$val.'" ';
        }

        return $att;
    }

    // Field Factory and Tracking
    public function addField($name, Fields\BaseField $field)
    {
        $this->fields[$name] = $field;
        return $field;
    }

    public function getField($name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function input($data = '', $value = '', $extra = '')
    {
        $name = is_array($data) ? (isset($data['name']) ? $data['name'] : '') : $data;
        $field = new Fields\Input($data, $value, $extra);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function textarea($data = '', $value = '', $extra = '')
    {
        $name = is_array($data) ? (isset($data['name']) ? $data['name'] : '') : $data;
        $field = new Fields\Textarea($data, $value, $extra);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function dropdown($data = '', $options = [], $selected = [], $extra = '')
    {
        $name = is_array($data) ? (isset($data['name']) ? $data['name'] : '') : $data;
        $field = new Fields\Select($data, $options, $selected, $extra);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function multiselect($name = '', $options = [], $selected = [], $extra = '')
    {
        $field = new Fields\Select($name, $options, $selected, $extra);
        $field->setMultiple(true);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        $name = is_array($data) ? (isset($data['name']) ? $data['name'] : '') : $data;
        $field = new Fields\Checkbox($data, $value, $checked, $extra);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function radio($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        $name = is_array($data) ? (isset($data['name']) ? $data['name'] : '') : $data;
        $field = new Fields\Radio($data, $value, $checked, $extra);
        $field->setParent($this);
        if ($name) $this->fields[$name] = $field;
        return $field->render();
    }

    public function submit($data = '', $value = '', $extra = '')
    {
        $field = new Fields\Button($data, $value, $extra);
        $field->setType('submit');
        return $field->render();
    }

    public function reset($data = '', $value = '', $extra = '')
    {
        $field = new Fields\Button($data, $value, $extra);
        $field->setType('reset');
        return $field->render();
    }

    public function button($data = '', $content = '', $extra = '')
    {
        $field = new Fields\Button($data, $content, $extra);
        $field->setIsButtonTag(true);
        return $field->render();
    }

    public function label($label_text = '', $id = '', $attributes = [])
    {
        $field = new Fields\Label($label_text, $id, $attributes);
        return $field->render();
    }

    public function hidden($name, $value = '', $recursing = FALSE)
    {
        $field = new Fields\Hidden($name, $value, $recursing);
        $field->setParent($this);
        return $field->render();
    }

    public function set_rules($field, $label = '', $rules = [])
    {
        if (is_array($field)) {
            foreach ($field as $row) {
                if (isset($row['field'], $row['rules'])) {
                    $this->set_rules($row['field'], isset($row['label']) ? $row['label'] : $row['field'], $row['rules']);
                }
            }
            return $this;
        }

        if ( ! isset($this->fields[$field])) {
            $this->fields[$field] = new Fields\Input($field);
            $this->fields[$field]->setParent($this);
        }
        
        $this->fields[$field]->setLabel($label)->setRules($rules);
        return $this;
    }

    public function run()
    {
        $this->error_array = [];
        $validation_array = $_POST;
        
        foreach ($this->fields as $name => $field) {
            $value = isset($validation_array[$name]) ? $validation_array[$name] : null;
            if ( ! $field->validate($value)) {
                $this->error_array[$name] = $field->getError();
            }
        }
        
        return empty($this->error_array);
    }

    public function set_value($field, $default = '', $html_escape = TRUE)
    {
        $value = $this->CI->input->post($field, FALSE);
        isset($value) OR $value = $default;
        return ($html_escape) ? html_escape((string)$value) : $value;
    }

    public function error($field = '', $prefix = '', $suffix = '')
    {
        if ( ! isset($this->error_array[$field])) return '';
        
        $prefix = $prefix ?: $this->error_prefix;
        $suffix = $suffix ?: $this->error_suffix;
        
        return $prefix . $this->error_array[$field] . $suffix;
    }

    public function validation_errors($prefix = '', $suffix = '')
    {
        if (empty($this->error_array)) return '';
        
        $prefix = $prefix ?: $this->error_prefix;
        $suffix = $suffix ?: $this->error_suffix;
        
        $str = '';
        foreach ($this->error_array as $error) {
            $str .= $prefix . $error . $suffix . "\n";
        }
        return $str;
    }

    public function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
    {
        $this->error_prefix = $prefix;
        $this->error_suffix = $suffix;
        return $this;
    }
}
