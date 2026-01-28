<?php namespace Admin\Services\Form\Fields;

class Label extends BaseField
{
    public function render()
    {
        $label = '<label';

        if ($this->value !== '') // using value as ID for label helper
        {
            $label .= ' for="'.$this->value.'"';
        }

        $label .= $this->_attributes_to_string($this->extra);

        return $label.'>'.$this->data.'</label>';
    }
}
