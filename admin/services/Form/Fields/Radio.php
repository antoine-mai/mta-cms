<?php namespace Admin\Services\Form\Fields;

class Radio extends Checkbox
{
    public function render()
    {
        if (is_array($this->data)) {
            $this->data['type'] = 'radio';
        } else {
            $this->data = ['name' => $this->data, 'type' => 'radio'];
        }
        return parent::render();
    }
}
