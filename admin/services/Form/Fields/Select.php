<?php namespace Admin\Services\Form\Fields;
/**
 * 
**/
class Select extends BaseField
{
    protected $options;
    protected $selected;
    protected $multiple = false;

    public function __construct($data = '', $options = [], $selected = [], $extra = '')
    {
        parent::__construct($data, '', $extra);
        $this->options = $options;
        $this->selected = $selected;
    }

    public function setMultiple($status)
    {
        $this->multiple = (bool)$status;
        return $this;
    }

    public function render()
    {
        $defaults = [];
        $data = $this->data;
        $selected = $this->selected;
        $options = $this->options;

        if (is_array($data))
        {
            if (isset($data['selected']))
            {
                $selected = $data['selected'];
                unset($data['selected']);
            }

            if (isset($data['options']))
            {
                $options = $data['options'];
                unset($data['options']);
            }
        }
        else
        {
            $defaults = ['name' => $data];
        }

        is_array($selected) OR $selected = [$selected];
        is_array($options) OR $options = [$options];

        if (empty($selected))
        {
            if (is_array($data))
            {
                if (isset($data['name'], $_POST[$data['name']]))
                {
                    $selected = [$_POST[$data['name']]];
                }
            }
            elseif (isset($_POST[$data]))
            {
                $selected = [$_POST[$data]];
            }
        }

        $extra = $this->_attributes_to_string($this->extra);
        if ($this->multiple && stripos($extra, 'multiple') === false)
        {
            $extra .= ' multiple="multiple"';
        }
        
        $multiple = (count($selected) > 1 && stripos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';

        $form = '<select '.rtrim($this->_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

        foreach ($options as $key => $val)
        {
            $key = (string) $key;

            if (is_array($val))
            {
                if (empty($val))
                {
                    continue;
                }

                $form .= '<optgroup label="'.\Admin\Core\Common::htmlEscape($key)."\">\n";

                foreach ($val as $optgroup_key => $optgroup_val)
                {
                    $sel = in_array((string)$optgroup_key, array_map('strval', $selected)) ? ' selected="selected"' : '';
                    $form .= '<option value="'.\Admin\Core\Common::htmlEscape((string)$optgroup_key).'"'.$sel.'>'
                        .(string) $optgroup_val."</option>\n";
                }

                $form .= "</optgroup>\n";
            }
            else
            {
                $form .= '<option value="'.\Admin\Core\Common::htmlEscape($key).'"'
                    .(in_array($key, array_map('strval', $selected)) ? ' selected="selected"' : '').'>'
                    .(string) $val."</option>\n";
            }
        }

        return $form."</select>\n";
    }
}
