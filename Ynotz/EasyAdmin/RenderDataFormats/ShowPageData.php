<?php
namespace Modules\Ynotz\EasyAdmin\RenderDataFormats;

class ShowPageData
{
    public string $title;
    public Object $instance;

    /**
     * construct
     *
     * @param string $title
     * @param Object $instance
     */
    public function __construct(string $title, Object $instance)
    {
        $this->title = $title;
        $this->instance = $instance;
    }

    /**
     * function getData
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            'title' => $this->title,
            'instance' => $this->instance
        ];
    }
}
?>
