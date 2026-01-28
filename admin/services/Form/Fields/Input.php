<?php namespace Admin\Services\Form\Fields;
/**
 * 
**/
class Input extends BaseField
{
    public function render()
    {
        $defaults = [
            'type' => 'text',
            'name' => is_array($this->data) ? '' : $this->data,
            'value' => $this->value
        ];

        return '<input '.$this->_parse_form_attributes($this->data, $defaults).$this->_attributes_to_string($this->extra)." />\n";
    }
}
