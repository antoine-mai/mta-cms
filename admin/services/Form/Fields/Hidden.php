<?php namespace Admin\Services\Form\Fields;

class Hidden extends BaseField
{
    protected $recursing;

    public function __construct($name, $value = '', $recursing = FALSE)
    {
        parent::__construct($name, $value);
        $this->recursing = $recursing;
    }

    public function render()
    {
        static $form;

        if ($this->recursing === FALSE)
        {
            $form = "\n";
        }

        if (is_array($this->data))
        {
            foreach ($this->data as $key => $val)
            {
                $h = new self($key, $val, TRUE);
                $form .= $h->render();
            }
            return $form;
        }

        if ( ! is_array($this->value))
        {
            $form .= '<input type="hidden" name="'.$this->data.'" value="'.html_escape((string)$this->value)."\" />\n";
        }
        else
        {
            foreach ($this->value as $k => $v)
            {
                $k = is_int($k) ? '' : $k;
                $h = new self($this->data.'['.$k.']', $v, TRUE);
                $form .= $h->render();
            }
        }

        return $form;
    }
}
