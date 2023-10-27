<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Illuminate\Database\Eloquent\Collection;

class TableHelper
{
    /**
     * Undocumented function
     *
     * @param string $title // table page title
     * @param Collection $xItems Collection of column items
     * @param Collection $yItems Collection of row items
     * @param string $actionRoute Route to uptade
     * @param string $xItemName
     * @param string $yItemName
     * @param string $xDisplayKey the name of the filed used to display in the cell
     * @param string $yDisplayKey the name of the filed used to display in the cell
     * @param string $idKey the key_colum_name to be provided in the action route to generate the update url
     * @return array
     */
    public static function buildCrossActionTableData(
        string $title,
        Collection $xItems,
        Collection $yItems,
        string $actionRoute,
        string $xItemName,
        string $yItemName,
        string $xDisplayKey,
        string $yDisplayKey,
        string $idKey = 'id',
    ): array
    {
        return [
            'title' => $title,
            'xItems' => $xItems,
            'yItems' => $yItems,
            'actionRoute' => $actionRoute,
            'xItemName' => $xItemName,
            'yItemName' => $yItemName,
            'xDisplayKey' => $xDisplayKey,
            'yDisplayKey' => $yDisplayKey,
            'idkey' => $idKey
        ];
    }
}

?>
