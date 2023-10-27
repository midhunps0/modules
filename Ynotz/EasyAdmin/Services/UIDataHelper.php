<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Modules\Ynotz\EasyAdmin\RenderDataFormats\ShowPageData;

class UIDataHelper
{
    public static function makeShowPageData($title, $instance, $layout = null): array
    {
        return (new ShowPageData($title, $instance, $layout))->getData();
    }
}
?>
