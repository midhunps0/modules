<?php
namespace Modules\Ynotz\EasyAdmin\Services;

class TabLayout extends LayoutElement
{
    protected $title = 'Tab';
    public function __construct(string $title, $width = "full")
    {
        parent::__construct($width);
        $this->type = 'tab';
        $this->title = $title;
    }

    public function getLayout(): array
    {
        $layout = parent::getLayout();
        $layout['title'] = $this->title;
        return $layout;
    }
}
?>
