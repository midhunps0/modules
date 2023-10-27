<?php
namespace Modules\Ynotz\AccessControl\Services;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Modules\Ynotz\AccessControl\Models\Role;
// use Modules\Ynotz\AccessControl\Models\User;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\Ynotz\EasyAdmin\Services\FormHelper;
use Modules\Ynotz\AccessControl\Services\RoleService;
use Modules\Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Modules\Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Modules\Ynotz\EasyAdmin\InputUpdateResponse;
use Modules\Ynotz\MediaManager\Services\EAInputMediaValidator;

class UserService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required',
            // 'photo' => (new EAInputMediaValidator())
            //     ->maxSize(1, 'mb')
            //     ->mimeTypes(['jpeg', 'jpg', 'png'])
            //     ->getRules(),
            'photo.*' => (new EAInputMediaValidator())
                ->maxSize(1, 'mb')
                ->mimeTypes(['jpeg', 'jpg', 'png'])
                ->getRules()
        ];
    }

    public $mediaFields = [
        'photo' => []
    ];

    public function __construct()
    {
        $this->modelClass = User::class;
    }

    private function getQuery()
    {
        return $this->modelClass::query()->with(['roles' => function ($query) {
            $query->select('name', 'id');
        }]);
    }

    protected function getIndexHeaders(): array
    {
        return [
            [
                'title' => 'Name',
                'sort' => ['key' => 'name'],
                'search' => ['key' => 'name', 'condition' => 'ct'],
                'search_label' => 'Search Users',
                'style' => 'width: 400px;'
            ],
            [
                'title' => 'Roles',
                'filter' => [
                    'key' => 'roles',
                    'options' => Role::all()->pluck('name', 'id')
                ],
                'style' => 'width: 300px;'
            ],
            [
                'title' => 'Action'
            ]
        ];
    }

    protected function getIndexColumns(): array
    {
        return [
            [
                'fields' => ['name'],
                'component' => 'text',
                'link' => [
                    'route' => 'users.show',
                    'key' => 'id'
                ]
            ],
            [
                'fields' => ['id', 'name'],
                'relation' => 'roles',
                'component' => 'text'
            ],
            [
                'edit_route' => 'users.edit',
                'component' => 'actions'
            ]
        ];
    }

    protected function relations(): array
    {
        return [
            'roles' => [
                'search_column' => 'id',
                'filter_column' => 'id'
                // 'search_fn' => function ($query, $op, $val) {
                //     $query->whereHas('roles', function ($q) use ($op, $val) {
                //         $q->where('name', $op, $val);
                //     });
                // }
            ],
        ];
    }

    protected function getAdvanceSearchFields(): array
    {
        return [
            'roles' => [
                'key' => 'roles',
                'text' => 'Roles',
                'type' => 'list_numeric',
                'inputType' => 'select',
                'options' => Role::all()->pluck('name', 'id'),
                'optionsType' => 'key_value' //value_only
            ]
        ];
    }

    public function getDownloadCols(): array
    {
        return [
            'id',
            'name'
        ];
    }

    public function getCreatePageData(): array
    {
        return [
            'title' => 'Create User',
            'form' => [
                'id' => 'form_user_create',
                'action_route' => 'users.store',
                'label_position' => 'top', //top/side/float
                'success_redirect_route' => 'users.index',
                'items' => [
                    FormHelper::makeInput(
                        inputType: 'text',
                        key: 'name',
                        label: 'Name',
                        properties: ['required' => true],
                        fireInputEvent: true
                    ),
                    FormHelper::makeInput('email', 'email', 'Email', ['required' => true]),
                    FormHelper::makeInput(
                        inputType: 'text',
                        key: 'password',
                        label: 'Password',
                        properties: ['required' => true, 'minlength' => 6],
                    ),
                    // FormHelper::makeInput(
                    //     inputType: 'text',
                    //     key: 'useslug',
                    //     label: 'Use Slug?',
                    //     properties: ['required' => true],
                    //     fireInputEvent: true
                    // ),
                    // FormHelper::makeInput(
                    //     inputType: 'text',
                    //     key: 'slug',
                    //     label: 'Slug',
                    //     properties: ['required' => true],
                    //     updateOnEvents: [
                    //         'name' => [
                    //             urlencode(Static::class),
                    //             'getSlug'
                    //         ]
                    //     ],
                    //     // toggleOnEvents: ['useslug' => [['==', 'No', false], ['==', 'Yes', true]]],
                    //     show: true,
                    //     authorised: true,
                    // ),
                    // FormHelper::makeSelect(
                    //     key: 'roles',
                    //     label: 'Role',
                    //     options: Role::all(),
                    //     options_type: 'collection',
                    //     options_id_key: 'id',
                    //     options_text_key: 'name',
                    //     options_src: [RoleService::class, 'suggestList'],
                    //     properties: [
                    //         'required' => true,
                    //         'multiple' => false
                    //     ],
                    // ),
                    FormHelper::makeSuggestlist(
                        key: 'role',
                        label: 'Role',
                        options_src: [RoleService::class, 'suggestList'],
                        options_type: 'collection',
                        options_id_key: 'id',
                        options_text_key: 'name',
                        resetOnEvents: ['name'],
                        properties: [
                            'required' => true,
                            'multiple' => true
                        ],
                        authorised: true,
                    ),
                    FormHelper::makeImageUploader(
                        key: 'photo',
                        label: 'Photo',
                        properties: ['required' => true, 'multiple' => true],
                        theme: 'regular',
                        allowGallery: true,
                        validations: [
                            'max_size' => '1 mb',
                            'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                            ]
                        // fireInputEvent: true
                    ),
                    FormHelper::makeDatePicker(
                        key: 'dob',
                        label: 'Date of birth',
                    ),
                    FormHelper::makeCheckbox(
                        key: 'verified',
                        label: 'Is verified?',
                        toggle: true,
                        displayText: ['Yes', 'No']
                    )
                ]
            ]
        ];
    }

    protected function getRelationQuery(int $id = null) {
        return null;
    }

    protected function accessCheck($item): bool
    {
        return true;
    }

    public function getSlug($text): InputUpdateResponse
    {
        return new InputUpdateResponse(
            Str::slug($text),
            'ok',
            true
        );
    }

    private function processBeforeStore(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        return $data;
    }
}
?>
