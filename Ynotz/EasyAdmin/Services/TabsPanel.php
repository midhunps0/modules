<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Modules\Ynotz\EasyAdmin\Services\TabLayout;
use Modules\Ynotz\EasyAdmin\Contracts\LayoutElementInterface;
use Modules\Ynotz\EasyAdmin\Exceptions\InvalidLayoutFormatException;

class TabsPanel implements LayoutElementInterface
{
    protected $tabs = [];
    protected $width = '';
    protected $properties = [];

    public function __construct(string $width = null, array $properties = null)
    {
        $this->width = $width ?? 'full';
        $this->properties = $properties ?? [];
    }

    public function getLayout(): array
    {
        return [
            'item_type' => 'layout',
            'layout_type' => 'tabs_panel',
            'width' => $this->width,
            'tabs' => $this->tabs,
            'properties' => $this->properties
        ];
    }

    public function addTab(TabLayout $tab): TabsPanel
    {
        $this->tabs[] = $tab->getLayout();
        return $this;
    }

    public function addTabs(array $tabs): TabsPanel
    {
        foreach ($tabs as $tab) {
            if (!($tab instanceof TabLayout)) {
                throw new InvalidLayoutFormatException(
                    "Attempt to add invalid element to TabsPanel. ".
                    "Only TabLayout instances can be added".
                    " as child elements of TabsPanel."
                );
            }
            $this->tabs[] = $tab->getLayout();
        }
        return $this;
    }

    public function setWidth(string $width): TabsPanel
    {
        $this->width = $width;
        return $this;
    }

    public function setProperty(string $key, string $value): TabsPanel
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function unsetProperty(string $key): TabsPanel
    {
        unset($this->properties[$key]);
        return $this;
    }
}
?>
