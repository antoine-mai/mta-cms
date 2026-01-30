<?php namespace Root\Services\Form\Fields;
/**
 * 
**/
class Checkbox extends BaseField
{
    protected $checked;

    public function __construct($data = '', $value = '', $checked = false, $extra = '')
    {
        parent::__construct($data, $value, $extra);
        $this->checked = $checked;
    }

    public function render()
    {
        $defaults = ['type' => 'checkbox', 'name' => ( ! is_array($this->data) ? $this->data : ''), 'value' => $this->value];

        if (is_array($this->data) && array_key_exists('checked', $this->data))
        {
            $this->checked = $this->data['checked'];

            if ($this->checked == false)
            {
                unset($this->data['checked']);
            }
            else
            {
                $this->data['checked'] = 'checked';
            }
        }

        if ($this->checked == true)
        {
            $defaults['checked'] = 'checked';
        }
        else
        {
            unset($defaults['checked']);
        }

        return '<input '.$this->_parse_form_attributes($this->data, $defaults).$this->_attributes_to_string($this->extra)." />\n";
    }
}
