<?php
namespace Modules\Ynotz\EasyAdmin\Services;

class SectionDivider extends LayoutElement
{
    protected $title;

    public function __construct($title, $width = 'grow')
    {
        parent::__construct($width);
        $this->title = $title;
    }

    public function getLayout(): array
    {
        return [
            'item_type' => 'layout',
            'layout_type' => 'divider',
            'title' => $this->title,
            'width' => $this->width,
            'properties' => $this->properties
        ];
    }
}
?>
