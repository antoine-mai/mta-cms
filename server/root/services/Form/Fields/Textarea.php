<?php namespace Root\Services\Form\Fields;
/**
 * 
**/
class Textarea extends BaseField
{
    public function render()
    {
        $defaults = [
            'name' => is_array($this->data) ? '' : $this->data,
            'cols' => '40',
            'rows' => '10'
        ];

        if ( ! is_array($this->data) OR ! isset($this->data['value']))
        {
            $val = $this->value;
        }
        else
        {
            $val = $this->data['value'];
            unset($this->data['value']); // textareas don't use the value attribute
        }

        return '<textarea '.$this->_parse_form_attributes($this->data, $defaults).$this->_attributes_to_string($this->extra).'>'
            .\Root\Core\Common::htmlEscape((string)$val)
            ."</textarea>\n";
    }
}
