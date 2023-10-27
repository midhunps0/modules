<?php
namespace Modules\Ynotz\EasyAdmin\RenderDataFormats;

class CreatePageData
{
    public string $title;
    public array $form;
    public Object|null $instance;

    /**
     * function __construct
     *
     * @param string $title
     * @param array $form
     * @param Object|null $instance
     */
    public function __construct(string $title, array $form, Object $instance = null)
    {
        $this->title = $title;
        $this->form = $form;
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
            'title' => 'Users',
            'form' => $this->form,
            '_old' => $this->instance
        ];
    }
}
?>
