<?php
namespace Modules\Ynotz\AppSettings\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Modules\Ynotz\EasyAdmin\Services\RowLayout;
use Modules\Ynotz\AppSettings\Models\AppSetting;
use Modules\Ynotz\EasyAdmin\InputUpdateResponse;
use Modules\Ynotz\EasyAdmin\Services\FormHelper;
use Modules\Ynotz\EasyAdmin\Services\IndexTable;
use Modules\Ynotz\AccessControl\Models\Permission;
use Modules\Ynotz\EasyAdmin\Services\ColumnLayout;
use Modules\Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Modules\Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Modules\Ynotz\EasyAdmin\RenderDataFormats\CreatePageData;
use Modules\Ynotz\EasyAdmin\RenderDataFormats\EditPageData;
use Modules\Ynotz\EasyAdmin\RenderDataFormats\ShowPageData;

class AppSettingService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $indexTable;
    private $user;

    public function __construct()
    {
        $this->modelClass = AppSetting::class;
        $this->indexTable = new IndexTable();
        $this->selectionEnabled = false;
        $this->exportsEnabled = false;
        // $this->user = auth()->user();
        // $this->showAddButton = $this->user->hasPermissionTo('System Settings: Create');
        /*
        $this->query = null;
        $this->idKey = 'id'; // id column in the db table to identify the items
        $this->selects = '*'; // query select keys/calcs
        $this->selIdsKey = 'id'; // selected items id key
        $this->searchesMap = []; // associative array mapping search query params to db columns
        $this->sortsMap = []; // associative array mapping sort query params to db columns
        $this->filtersMap = [];
        $this->orderBy = ['created_at', 'desc'];
        $this->sqlOnlyFullGroupBy = true;
        $this->defaultSearchColumn = 'name';
        $this->defaultSearchMode = 'startswith'; // contains, startswith, endswith
        $this->relations = [];
        $this->downloadFileName = 'results';
        */
    }

    public function getShowAddButton()
    {
        return auth()->user()->hasPermissionTo('System Settings: Create');
    }

    protected function getIndexHeaders(): array
    {;
        $header = $this->indexTable->addHeaderColumn(
            title: 'Settings Name',
            search: ['key' => 'name', 'condition' => 'ct', 'label' => 'Search by name'],
            sort: ['key' => 'name']
        );
        if (auth()->user()->hasPermissionTo('System Settings: Create')){
            $header = $header->addHeaderColumn(
                title: 'Value Type'
            );
        }
        return $header->addHeaderColumn(
            title: 'Actions'
        )->getHeaderRow();
    }

    protected function getIndexColumns(): array
    {
        $column = $this->indexTable
            ->addColumn(
                fields: ['name'],
            );
        if (auth()->user()->hasPermissionTo('System Settings: Create')){
            $column = $column->addColumn(
                fields: ['value_type']
            );
        }
        return $column->addActionColumn(
            editRoute: 'appsettings.edit',
            deleteRoute: 'appsettings.destroy',
            deletePermission: auth()->user()->hasPermissionTo('System Settings: Delete')
        )
        ->getRow();
    }

    private function formElements(Model $instance = null): array
    {
        /**
         * @var User
         */
        $user = auth()->user();
        return [
            'name' => FormHelper::makeInput(
                inputType: 'text',
                key: 'name',
                label: 'Name',
                properties: ['readonly' => true]
            ),
            'value_type' => FormHelper::makeSelect(
                key: 'value_type',
                label: 'Value type',
                options: [
                        'bigInteger' => 'Big Integer',
                        'integer' => 'Integer',
                        'double' => 'Double',
                        'boolean' => 'Boolean',
                        'date' => 'Date',
                        'datetime' => 'Date Time',
                        'text' => 'Text',
                        'json' => 'Json',
                    ],
                options_type: 'key_value',
                authorised: $user->hasPermissionTo('System Settings: Create')
            ),
            'auto_manage' => FormHelper::makeCheckbox(
                key: 'auto_manage',
                label: 'Auto manage?',
                displayText: ['Yes', 'No'],
                authorised: $user->hasPermissionTo('System Settings: Create')
            ),
            'value_edit' => $this->getValueInputElement(
                instance: $instance,
                key: 'value',
                label: 'Value',
            ),
            'value' => FormHelper::makeTextarea(
                key: 'value',
                label: 'value',
                authorised: $user->hasPermissionTo('System Settings: Create'),
                formTypes: ['create']
            ),
            'view_permissions' => FormHelper::makeSuggestlist(
                key: 'view_permissions',
                label: 'View permissions',
                options_src: [AppSettingService::class, 'permissionsList'],
                authorised: $user->hasPermissionTo('System Settings: Create'),
                properties: ['multiple' => true]
            ),
            'edit_permissions' => FormHelper::makeSuggestlist(
                key: 'edit_permissions',
                label: 'Edit permissions',
                options_src: [AppSettingService::class, 'permissionsList'],
                authorised: $user->hasPermissionTo('System Settings: Edit'),
                properties: ['multiple' => true]
            ),
            'delete_permissions' => FormHelper::makeSuggestlist(
                key: 'delete_permissions',
                label: 'Delete permissions',
                options_src: [AppSettingService::class, 'permissionsList'],
                authorised: $user->hasPermissionTo('System Settings: Delete'),
                properties: ['multiple' => true]
            ),
        ];
    }

    private function getValueInputElement(
        $instance,
        string $key,
        string $label = null,
        bool $toggle = false,
        array|null $displayText = null,
        array $options_src = null,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        array $options_display_keys = [],
        string $list_component = 'easyadmin::inputs.parts.sgls_regular_list',
        string $none_selected = 'Select One',
        int $startYear = 2000,
        int $endYear = 2050,
        string $dateFormat = 'DD-MM-YYYY',
        array $properties = null,
        bool $fireInputEvent = false,
        array $updateOnEvents = null,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array {
        $r = [];
        if ($instance == null) {
            return FormHelper::makeInput(
                'text',
                $key,
                $label,
                $properties,
                $fireInputEvent,
                $updateOnEvents,
                $resetOnEvents,
                $toggleOnEvents,
                $formTypes,
                $show,
                $authorised,
                $width,
            );
        }
        switch($instance->value_type) {
            case 'boolean';
                $r = FormHelper::makeCheckbox(
                    $key,
                    $label,
                    $toggle,
                    $displayText,
                    $properties,
                    $fireInputEvent,
                    $resetOnEvents,
                    $toggleOnEvents,
                    $formTypes,
                    $show,
                    $authorised,
                    $width,
                );
                break;
            case 'date';
                $r = FormHelper::makeDatePicker(
                    $key,
                    $label,
                    $startYear,
                    $endYear,
                    $dateFormat,
                    $properties,
                    $fireInputEvent,
                    $updateOnEvents,
                    $resetOnEvents,
                    $toggleOnEvents,
                    $formTypes,
                    $show,
                    $authorised,
                    $width,
                );
                break;
            case 'json':
                $r = FormHelper::makeInput(
                    $instance->form_component,
                    $key,
                    $label,
                    $properties,
                    $fireInputEvent,
                    $updateOnEvents,
                    $resetOnEvents,
                    $toggleOnEvents,
                    $formTypes,
                    $show,
                    $authorised,
                    $width,
                );
                break;
            default:
                $r = FormHelper::makeInput(
                    'text',
                    $key,
                    $label,
                    $properties,
                    $fireInputEvent,
                    $updateOnEvents,
                    $resetOnEvents,
                    $toggleOnEvents,
                    $formTypes,
                    $show,
                    $authorised,
                    $width,
                );
                break;
        }
        return $r;
    }

    public function getShowPageData($id)
    {
        $instance = AppSetting::find($id);
        $title = 'App Setting: ' . $instance->name;
        return new ShowPageData($title, $instance);
    }

    public function getCreatePageData(): CreatePageData
    {
        $sn = $this->getModelShortName();
        return new CreatePageData(
            title: 'App Settings',
            form: FormHelper::makeForm(
                title: 'Create '.$sn,
                id: 'form_'.Str::lower(Str::plural($sn)).'_create',
                action_route: Str::lower(Str::plural($sn)) . '.store',
                success_redirect_route: Str::lower(Str::plural($sn)). '.show',
                // success_redirect_key: 'id',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: '1/2',
                type: 'easyadmin::partials.simpleform',
            )
        );
    }

    public function getEditPageData($keyVal = null, $keyCol = 'id'): EditPageData
    {
        $instance = $this->modelClass::where($keyCol, $keyVal)
            ->get()->first();
        $sn = $this->getModelShortName();
        return new EditPageData(
            title: 'users',
            form: FormHelper::makeEditForm(
                title: 'Edit '.$sn,
                id: 'form_'.Str::lower(Str::plural($sn)).'_edit',
                action_route: Str::lower(Str::plural($sn)) . '.update',
                action_route_params: [$keyCol => $instance->$keyCol],
                success_redirect_route: Str::lower(Str::plural($sn)). '.index',
                cancel_route: 'dashboard',
                items: $this->getEditFormElements($instance),
                layout: $this->buildEditFormLayout(),
                label_position: 'side',
                width: 'w-3/4',
                type: 'easyadmin::partials.simpleform',
            ),
            instance: $instance
        );
        return [
            'title' => Str::plural($sn),
            'form' => FormHelper::makeEditForm(
                title: 'Edit '.$sn,
                id: 'form_'.Str::lower(Str::plural($sn)).'_edit',
                action_route: Str::lower(Str::plural($sn)) . '.update',
                action_route_params: [$keyCol => $instance->$keyCol],
                success_redirect_route: Str::lower(Str::plural($sn)). '.show',
                cancel_route: 'dashboard',
                items: $this->getEditFormElements($instance),
                layout: $this->buildEditFormLayout(),
                label_position: 'side',
                width: 'w-3/4',
                type: 'easyadmin::partials.simpleform',
            ),
            '_old' => $instance,
            '_current_values' => $instance,
        ];
    }

    public function buildCreateFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('name'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('value_type'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout(style: 'justify-content: flex-end'))->addInputSlot('auto_manage'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('value'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('view_permissions'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('edit_permissions'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('delete_permissions'),
                        ]
                    ),
                ]
            );
        return $layout->getLayout();
    }

    public function buildEditFormLayout(): array
    {
        $layout = (new ColumnLayout())
            ->addElements(
                [
                    (new RowLayout(width: 'full'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('name'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('value_type'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout(style: 'justify-content: flex-end'))->addInputSlot('auto_manage'),
                        ]
                    ),
                    (new RowLayout(width: 'full'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('value_edit'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('view_permissions'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('edit_permissions'),
                        ]
                    ),
                    (new RowLayout(width: '1/4'))->addElements(
                        [
                            (new ColumnLayout())->addInputSlot('delete_permissions'),
                        ]
                    ),
                ]
            );
        return $layout->getLayout();
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:app_settings,name'],
            'slug' => ['required', 'string', 'unique:app_settings,slug'],
            'value_type' => ['required', 'string'],
            'value' => ['sometimes',],
            'auto_manage' => ['required', 'boolean'],
            'view_permissions' => ['sometimes', 'nullable', 'array'],
            'edit_permissions' => ['sometimes', 'nullable', 'array'],
            'delete_permissions' => ['sometimes', 'nullable', 'array'],
            'view_permissions.*' => ['sometimes', 'integer'],
            'edit_permissions.*' => ['sometimes', 'integer'],
            'delete_permissions.*' => ['sometimes', 'integer']
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'value_type' => ['sometimes', 'string'],
            'value' => ['sometimes',],
            'auto_manage' => ['sometimes', 'boolean'],
            'view_permissions' => ['sometimes', 'nullable', 'array'],
            'edit_permissions' => ['sometimes', 'nullable', 'array'],
            'delete_permissions' => ['sometimes', 'nullable', 'array'],
            'view_permissions.*' => ['sometimes', 'integer'],
            'edit_permissions.*' => ['sometimes', 'integer'],
            'delete_permissions.*' => ['sometimes', 'integer']
        ];
    }

    public function prepareForStoreValidation($data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['auto_manage'] = boolval($data['auto_manage']);
        return $data;
    }

    public function prepareForUpdateValidation($data)
    {
        $data['slug'] = Str::slug($data['name']);
        if (isset($data['auto_manage'])) {
            $data['auto_manage'] = boolval($data['auto_manage']);
        }
        return $data;
    }

    public function authoriseCreate(): bool
    {
        return auth()->user()->hasPermissionTo('App Settings: Create');
    }

    public function authoriseEdit(): bool
    {
        return auth()->user()->hasPermissionTo('App Settings: Edit');
    }

    public function permissionsList(): InputUpdateResponse
    {
        return new InputUpdateResponse(
            result: Permission::all(),
            message: 'Success',
            isvalid: true
        );
    }

}
