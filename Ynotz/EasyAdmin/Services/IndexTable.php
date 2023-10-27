<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Modules\Ynotz\EasyAdmin\Exceptions\InvalidAdvSearchAttributeException;

class IndexTable
{
    private $headerRow = [];
    private $row = [];
    private $searchFields = [];

    /**
     * Function makeIndexHeader Returns array for making an index column header
     *
     * @param string $title
     * @param array $search Eg. Format: [key' => 'name', 'condition' => 'st|en|ct', 'label' => 'Search Members' ]. Key: name of the column. Condition: st -> Starts With, en -> Ends with, ct -> contains
     * @param array $sort Eg. Format: [key' => 'name']
     * @param array $filter Eg. Format: [key' => 'name', 'options' => Books::all()->pluck('name', 'id')]
     * @return IndexTable
     */
    public function addHeaderColumn(
        string $title,
        array $search = null,
        array $sort = null,
        array $filter = null
    ): IndexTable
    {
        $data = [
            'title' => $title,
        ];
        if (isset($search)) {
            $data['search'] = $search;
        }
        if (isset($sort)) {
            $data['sort'] = $sort;
        }
        if (isset($filter)) {
            $data['filter'] = $filter;
        }
        $this->headerRow[] = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $fields Database columns to be displayed in this field. Eg. Format: ['id', 'title',]
     * @param string $component The blade component to be rendered
     * @param string $relation Optional. If the field specified belongs to a relation, specify that relation here.
     * @param array $link Hyperlink to be applied to the displayed content. Eg. Format: ['route' => 'books.show', 'key' => 'id']. route: The route to show the model details (Could be an admin route or a public route). key: The database table colum value which is to be used as the route parameter.
     * @return IndexTable
     */
    public function addColumn(
        array $fields,
        array $imageFields = [],
        string $component = 'easyadmin::display.text',
        string $relation = null,
        array $link = null
    ): IndexTable
    {
        $data = [
            'fields' => $fields,
            'image_fields' => $imageFields,
            'component' => $component
        ];
        if (isset($relation)) {
            $data['relation'] = $relation;
        }
        if (isset($link)) {
            $data['relation'] = $link;
        }

        $this->row[] = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $editRoute
     * @param string $deleteRoute
     * @param string $uniqueKey The edit/delete route param (unique identifier of the model). Default 'id'
     * @param string $component The component to be rendered
     * @return IndexTable
     */
    public function addActionColumn(
        string $viewRoute = null,
        string $editRoute = null,
        string $deleteRoute = null,
        bool $viewPermission = true,
        bool $editPermission = true,
        bool $deletePermission = true,
        string $uniqueKey = 'id',
        string $component = 'easyadmin::display.actions'
    ): IndexTable
    {
        $data = [
            'unique_key' => $uniqueKey,
            'component' => $component
        ];

        if (isset($viewRoute) && $viewPermission) {
            $data['view_route'] = $viewRoute;
        }

        if (isset($editRoute) && $editPermission) {
            $data['edit_route'] = $editRoute;
        }

        if (isset($deleteRoute) && $deletePermission) {
            $data['delete_route'] = $deleteRoute;
        }

        $this->row[] = $data;

        return $this;
    }

    public function getHeaderRow()
    {
        return $this->headerRow;
    }

    public function getRow()
    {
        return $this->row;
    }

    /**
     * Undocumented function
     *
     * @param string $key The column name
     * @param string $displayText The text to be displayed as the search field
     * @param string $valueType The input value type. Allowed values: numeric|string|list_numeric|list_string.
     * @param array $options Allowed types: [key1 => value1, ...] OR [value1, value2...]
     * @param string|null $optionsType Allowed values: 'key_value', 'value_only'.
     * @return IndexTable
     */
    public function addSearchField(
        string $key,
        string $displayText,
        string $valueType,
        array $options = [],
        string $optionsType = null
    ): IndexTable
    {
        $data = [
            'key' => $key,
            'display_text' => $displayText,
            'input_val_type' => $valueType,
        ];
        if (in_array($valueType, ['numeric', 'string'])) {
            $data['input_elm_type'] = 'text';
        } else if (in_array($valueType, ['list_numeric', 'list_string'])) {
            $data['input_elm_type'] = 'select';
        } else {
            throw new InvalidAdvSearchAttributeException(
                "The valueType " . $valueType . " is invalid. Key: " . $key
                . " The valueType shall be one of"
                . " numeric, string, list_numeric or list_string."
            );
        }
        if (isset($options) && count($options) > 0) {
            $data['options'] = $options;
            if (in_array($optionsType, ['key_value', 'value_only'])) {
                $data['options_type'] = $optionsType;
            } else {
                throw new InvalidAdvSearchAttributeException(
                    "The optionsType " . $optionsType . " is invalid. Key: " . $key
                    . " Expected optionTypes: 'key_vaue', 'value_only'"
                );
            }
        }

        $this->searchFields[$key] = $data;

        return $this;
    }

    /**
     * getAdvSearchFields returns the advanced search fields array. Chain it along with addSearchField()
     *
     * @return array
     */
    public function getAdvSearchFields(): array
    {
        return $this->searchFields;
    }
}
?>
