<?php namespace Admin\Services\Form\Fields;
/**
 * 
**/
class Button extends BaseField
{
    protected $type = 'button';
    protected $isButtonTag = false;

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setIsButtonTag($status)
    {
        $this->isButtonTag = (bool)$status;
        return $this;
    }

    public function render()
    {
        if ($this->isButtonTag)
        {
            $defaults = [
                'name' => is_array($this->data) ? '' : $this->data,
                'type' => $this->type
            ];

            $content = $this->value; // For button tag, value is usually content
            if (is_array($this->data) && isset($this->data['content']))
            {
                $content = $this->data['content'];
                unset($this->data['content']);
            }

            return '<button '.$this->_parse_form_attributes($this->data, $defaults).$this->_attributes_to_string($this->extra).'>'
                .$content
                ."</button>\n";
        }

        $defaults = [
            'type' => $this->type,
            'name' => is_array($this->data) ? '' : $this->data,
            'value' => $this->value
        ];

        return '<input '.$this->_parse_form_attributes($this->data, $defaults).$this->_attributes_to_string($this->extra)." />\n";
    }
}
