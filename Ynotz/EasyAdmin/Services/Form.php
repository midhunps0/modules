<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Modules\Ynotz\EasyAdmin\Services\LayoutElement;

class Form extends LayoutElement
{
    private $data;

    public function __construct(
        string $title,
        string $id,
        string $action_route,
        string $success_redirect_route,
        string $type,
        array $items = [],
        array $layout = [],
        array $action_route_params = [],
        string $cancel_route = null,
        string $label_position = 'float',
        string $width = '1/2',
        $submitLabel = 'Submit',
        $cancelLabel = 'Cancel',
        string $method = "POST",
    ) {
        $this->data = [
            'title' => $title,
            'id' => $id,
            'action_route' => $action_route,
            'action_route_params' => $action_route_params,
            'success_redirect_route' => $success_redirect_route,
            'cancel_route' => $cancel_route ?? $success_redirect_route,
            'items' => $items,
            'layout' => $layout,
            'label_position' => $label_position,
            'type' => $type,
            'width' => $width,
            'submit_label' => $submitLabel,
            'cancel_label' => $cancelLabel,
            'method' => $method
        ];
    }

    public function addElement(array|LayoutElement $el): Form
    {
        $this->data[] = $el;
        return $this;
    }

    public function addTab(): Form
    {
        $this->data[] = new LayoutElement('form_tab');
        return $this;
    }
}
?>
