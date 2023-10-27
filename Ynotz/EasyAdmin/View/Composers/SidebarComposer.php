<?php
namespace Modules\Ynotz\EasyAdmin\View\Composers;

use Illuminate\Contracts\View\View;
use UnexpectedValueException;
use Modules\Ynotz\EasyAdmin\Contracts\SidebarServiceInterface;

class SidebarComposer
{
    // private $dataSource;

    // public function __construct(SidebarServiceInterface $source)
    // {
    //     $this->dataSource = $source;
    // }

    public function compose(View $view)
    {
        $data = [];
        foreach (config('easyadmin.sidebar_services') as $ds) {
            $sidebarService = new $ds();
            if (!($sidebarService instanceof SidebarServiceInterface)) {
                throw new UnexpectedValueException('The given class '. $ds .' is not an instance of SidebarServiceInterface');
            }
            $data = array_merge($data, $sidebarService->getSidebarData());
        }
        $view->with([
            'sidebar_data' => $data
        ]);
    }
}
?>
