<?php
namespace Modules\Ynotz\EasyAdmin\Services;

class ColumnLayout extends UILayout
{
    public function __construct($width = "grow", $style = '')
    {
        parent::__construct($width, $style);
        $this->type = 'column';
        $this->style = $style;
    }
}
?>
