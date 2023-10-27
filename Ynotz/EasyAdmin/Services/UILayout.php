<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Modules\Ynotz\EasyAdmin\Contracts\UILayoutInterface;
use Modules\Ynotz\EasyAdmin\Exceptions\InvalidLayoutFormatException;

class UILayout implements UILayoutInterface
{
    protected $type;
    protected $width;
    protected $style;
    protected $elements = [];
    protected $properties = [];

    public function __construct(string $width = null, string $style = '')
    {
        $this->width = $width ?? 'full';
        $this->style = $style;
    }

    public function getLayout(): array
    {
        return [
            'item_type' => 'layout',
            'layout_type' => $this->type,
            'width' => $this->width,
            'style' => $this->style,
            'items' => $this->elements,
            'properties' => $this->properties
        ];
    }

    public function addElement(UILayout $el): UILayout
    {
        if (!$this->hasInputElement()) {
            $this->elements[] = $el->getLayout();
        } else {
            throw new InvalidLayoutFormatException("Can't add layout element to the " . $this->type .". The " . $this->type . " already has an input slot.");
        }
        return $this;
    }

    public function addElements(array $elements): UILayout
    {
        foreach ($elements as $el) {
            if (!$this->hasInputElement()) {
                $this->elements[] = $el->getLayout();
            } else {
                throw new InvalidLayoutFormatException("Can't add layout element to the " . $this->type .". The " . $this->type . " already has an input slot.");
            }
        }
        return $this;
    }

    public function addInputSlot(
        string $key
    ): UILayout {
        $this->elements[] = [
            'item_type' => 'input',
            'key' => $key
        ];
        return $this;
    }

    public function addInputSlots(
        array $keys
    ): UILayout {
        foreach ($keys as $key) {
            $this->elements[] = [
                'item_type' => 'input',
                'key' => $key
            ];
        }
        return $this;
    }

    private function hasInputElement(): bool
    {
        $status = false;
        foreach ($this->elements as $el) {
            if ($el['item_type'] == 'input') {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function setWidth(string $width): UILayout
    {
        $this->width = $width;
        return $this;
    }

    public function setProperty(string $key, string $value): UILayout
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function unsetProperty(string $key): UILayout
    {
        unset($this->properties[$key]);
        return $this;
    }
}
?>
