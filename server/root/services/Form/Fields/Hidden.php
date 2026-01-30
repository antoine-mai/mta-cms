<?php namespace Root\Services\Form\Fields;
/**
 * 
**/
class Hidden extends BaseField
{
    protected $recursing;

    public function __construct($name, $value = '', $recursing = false)
    {
        parent::__construct($name, $value);
        $this->recursing = $recursing;
    }

    public function render()
    {
        static $form;

        if ($this->recursing === false)
        {
            $form = "\n";
        }

        if (is_array($this->data))
        {
            foreach ($this->data as $key => $val)
            {
                $h = new self($key, $val, true);
                $form .= $h->render();
            }
            return $form;
        }

        if ( ! is_array($this->value))
        {
            $form .= '<input type="hidden" name="'.$this->data.'" value="'.\Root\Core\Common::htmlEscape((string)$this->value)."\" />\n";
        }
        else
        {
            foreach ($this->value as $k => $v)
            {
                $k = is_int($k) ? '' : $k;
                $h = new self($this->data.'['.$k.']', $v, true);
                $form .= $h->render();
            }
        }

        return $form;
    }
}
