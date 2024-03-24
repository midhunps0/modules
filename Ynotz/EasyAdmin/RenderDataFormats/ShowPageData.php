<?php
namespace Modules\Ynotz\EasyAdmin\RenderDataFormats;

class ShowPageData
{
    public string $title;
    public Object $instance;
    public array $data;

    /**
     * construct
     *
     * @param string $title
     * @param Object $instance
     */
    public function __construct(string $title, Object $instance, array $data = null)
    {
        $this->title = $title;
        $this->instance = $instance;
        $this->data = $data;
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
            'instance' => $this->instance,
            'data' => $this->data
        ];
    }
}
?>
