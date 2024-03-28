<?php
namespace Modules\Ynotz\EasyAdmin\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Query\Builder;
use Modules\Ynotz\EasyAdmin\InputUpdateResponse;
use Modules\Ynotz\EasyAdmin\Services\FormHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Ynotz\EasyAdmin\RenderDataFormats\CreatePageData;
use Modules\Ynotz\EasyAdmin\RenderDataFormats\ShowPageData;

trait IsModelViewConnector{
    protected $modelClass;
    protected $query = null;
    protected $idKey = 'id'; // id column in the db table to identify the items
    protected $selects = '*'; // query select keys/calcs
    protected $selIdsKey = 'id'; // selected items id key
    protected $searchesMap = []; // associative array mapping search query params to db columns
    protected $sortsMap = []; // associative array mapping sort query params to db columns
    protected $filtersMap = [];
    protected $orderBy = ['created_at', 'desc'];
    // protected $uniqueSortKey = null; // unique key to sort items. it can be a calculated field to ensure unique values
    protected $sqlOnlyFullGroupBy = true;
    protected $defaultSearchColumn = 'name';
    protected $defaultSearchMode = 'startswith'; // contains, startswith, endswith
    protected $relations = [];
    protected $processRelationsManually = false;
    protected $processMediaManually = false;
    protected $selectionEnabled = false;
    protected $exportsEnabled = true;

    public $downloadFileName = 'results';

    public function index(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        bool $indexMode,
        string $selectedIds = '',
        string $resultsName = 'results',
    ): array {
        $name = ucfirst(Str::plural(Str::lower($this->getModelShortName())));
        if (!$this->authoriseIndex()) {
            throw new AuthorizationException('The user is not authorised to view '.$name.'.');
        }
        $this->preIndexExtra();

        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams
        );

        if ($indexMode
            || count($searches) > 0
            || count($sorts) > 0
            || count($filters) > 0
            || count($advParams) > 0
        ) {
            $results = $queryData['query']->orderBy(
                $this->orderBy[0],
                $this->orderBy[1]
            )->paginate(
                $itemsCount,
                $this->selects,
                'page',
                $page
            );

            $this->postIndexExtra();
            $data = $results->toArray();
        } else {
            $data = [];
        }

        return [
            'results' => $results,
            'searches' => $queryData['searchParams'],
            'sorts' => $queryData['sortParams'],
            'filters' => $queryData['filterData'],
            'adv_params' => $queryData['advParams'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'selected_ids' => $selectedIds,
            'selectIdsUrl' => $this->getSelectedIdsUrl(),
            'total_results' => $data['total'],
            // 'current_page' => $data['current_page'],
            // 'paginator' => json_encode($paginator),
            'downloadUrl' => $this->getDownloadUrl(),
            'createRoute' => $this->getCreateRoute(),
            'destroyRoute' => $this->getDestroyRoute(),
            'editRoute' => $this->getEditRoute(),
            'route' => Request::route()->getName(),
            'showAddButton' => $this->getshowAddButton(),
            'selectionEnabled' => $this->getSelectionEnabled(),
            'exportsEnabled' => $this->getExportsEnabled(),
            'advSearchFields' => $this->getAdvanceSearchFields(),
            'col_headers' => $this->getIndexHeaders(),
            'columns' => $this->getIndexColumns(),
            'title' => $this->getPageTitle(),
            'index_id' => $this->getIndexId(),
        ];
    }

    public function show($id)
    {
        if (count($this->showWith())) {
            $item = $this->modelClass::with($this->showWith())
                ->where($this->idKey, $id)->get()->first();
        } else {
            $item = $this->modelClass::where($this->idKey, $id)->get()->first();
        }
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseShow($item)) {
            throw new AuthorizationException('The user is not authorised to view '.$name.'.');
        }
        return [
            'item' => $item
        ];
    }

    public function showWith(): array
    {
        return [];
    }

    private function getQuery()
    {
        return $this->query ?? $this->modelClass::query();
    }

    public function getShowPageData($id): ShowPageData
    {
        return new ShowPageData(
            Str::ucfirst($this->getModelShortName()),
            $this->getQuery()->where($this->key, $id)->get()->first()
        );
    }

    private function getItemIds($results) {
        $ids = $results->pluck($this->idKey)->toArray();
        return json_encode($ids);
    }

    public function indexDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams,
            $selectedIds
        );
            // DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        // DB::statement("SET SQL_MODE='only_full_group_by'");

        return $this->formatIndexResults($results->toArray());
    }

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters
        );

        // DB::statement("SET SQL_MODE=''");

        $results = $queryData['query']->select($this->selects)->get()->pluck($this->idKey)->unique()->toArray();
        // DB::statement("SET SQL_MODE='only_full_group_by'");
        return $results;
    }

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams = [],
        string $selectedIds = ''
    ): array {
        $query = $this->getQuery();

        // if (count($relations = $this->relations()) > 0) {
        //     $query->with(array_keys($relations));
        // }

        $filterData = $this->getFilterParams($query, $filters, $this->filtersMap);
        $searchParams = $this->getSearchParams($query, $searches, $this->searchesMap);
        $sortParams = $this->getSortParams($query, $sorts, $this->sortsMap);
        $advParams = $this->getSearchParams($query, $advParams, $this->searchesMap);
        // $this->extraConditions($query);

        if (isset($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            // $this->query->whereIn('c.id', $ids);
            $this->querySelectedIds($query, $this->selIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData,
            'advParams' => $advParams
        ];
    }

    public function getItem(string $id)
    {
        return $this->modelClass::find($id);
    }

    public function store(array $data): Model
    {
        $data = $this->processBeforeStore($data);
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseStore()) {
            throw new AuthorizationException('Unable to create the '.$name.'. The user is not authorised for this action.');
        }
        //filter out relationship fields from $data
        $ownFields = [];
        $relations = [];
        $mediaFields = [];

        foreach ($data as $key => $value) {
            if ($this->isRelation($key)) {
                $relations[$key] = $value;
            } elseif ($this->isMideaField($key)) {
                $mediaFields[$key] = $value;
            } else {
                $ownFields[$key] = $value;
            }
        }

        DB::beginTransaction();
        try {
            $instance = $this->modelClass::create($ownFields);

            //attach relationship instances as per the relation
            if (!$this->processRelationsManually) {
                foreach ($relations as $rel => $val) {
                    if (isset($this->relations()[$rel]['store_fn'])) {
                        ($this->relations()[$rel]['store_fn'])($instance, $val, $data);
                    } else {
                        $type = $this->getRelationType($rel);
                        switch ($type) {
                            case 'BelongsTo':
                                $instance->$rel()->associate($val);
                                $instance->save();
                                break;
                            case 'BelongsToMany':
                                $instance->$rel()->attach($val);
                                break;
                            case 'HasOne':
                                $cl = $instance->$rel()->getRelated();
                                $fkey = $instance->$rel()->getForeignKeyName();
                                $lkey = $instance->$rel()->getLocalKeyName();
                                $darray = array_merge([$fkey => $instance->$lkey], $val);
                                $cl::create($darray);
                                break;
                            case 'HasMany':
                                $instance->$rel()->delete();
                                $t = array();
                                foreach ($val as $v) {
                                    if (is_array($v)) {
                                        $t[] = $instance->$rel()->create($v);
                                    }
                                }
                                $instance->$rel()->saveMany($t);
                        }
                    }
                }
            } else {
                $this->processRelationsAfterStore($instance, $relations);
            }
            if (!$this->processMediaManually) {
                foreach ($mediaFields as $fieldName => $val) {
                    $instance->addMediaFromEAInput($fieldName, $val);
                }
            } else {
                $this->processMediaAfterStore($instance, $mediaFields);
            }
            DB::commit();
            $this->processAfterStore($instance);
            return $instance;
        } catch (\Exception $e) {
            DB::rollBack();
            info($e->__toString());
            throw new Exception($e->__toString());
        }
    }

    public function update($id, array $data)
    {
        $data = $this->processBeforeUpdate($data, $id);
        info('data');
        info($data);
        $instance = $this->modelClass::find($id);
        $oldInstance = $instance;
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseUpdate($instance)) {
            throw new AuthorizationException('Unable to update the '.$name.'. The user is not authorised for this action.');
        }
        $ownFields = [];
        $relations = [];
        $mediaFields = [];
        foreach ($data as $key => $value) {
            if ($this->isRelation($key)) {
                $relations[$key] = $value;
            } elseif ($this->isMideaField($key)) {
                $mediaFields[$key] = $value;
            } else {
                $ownFields[$key] = $value;
            }
        }
        info('ownFields');
        info($ownFields);
        DB::beginTransaction();
        try {
            $instance->update($ownFields);
            if (!$this->processRelationsManually) {
            //attach relationship instances as per the relation
                foreach ($relations as $rel => $val) {
                    if (isset($this->relations()[$rel]['update_fn'])) {
                        ($this->relations()[$rel]['update_fn'])($instance, $val, $data);
                    } else {
                        $type = $this->getRelationType($rel);
                        switch ($type) {
                            case 'BelongsTo':
                                $instance->$rel()->associate($val);
                                $instance->save();
                                break;
                            case 'BelongsToMany':
                                $instance->$rel()->sync($val);
                                break;
                            case 'HasOne':
                                $relInst = $instance->$rel;
                                $relInst->update($val);
                                break;
                            case 'HasMany':
                                $instance->$rel()->delete();
                                $t = array();
                                foreach ($val as $v) {
                                    if (is_array($v)) {
                                        $t[] = $instance->$rel()->create($v);
                                    }
                                }
                                $instance->$rel()->saveMany($t);
                                break;
                        }
                    }
                }
            } else {
                $this->processRelationsAfterUpdate($instance, $relations);
            }
            if (!$this->processMediaManually) {
                foreach ($mediaFields as $fieldName => $val) {
                    $instance->syncMedia($fieldName, $val);
                }
            } else {
                $this->processMediaAfterUpdate($instance, $mediaFields);
            }

//             foreach ($mediaFields as $fieldName => $val) {
//                 $instance->addMediaFromEAInput($fieldName, $val);
//             }
            DB::commit();
            $this->processAfterUpdate($oldInstance, $instance);
        } catch (\Exception $e) {
            info('rolled back: '.$e->__toString());
            DB::rollBack();
        }

        return $instance;
    }

    public function processAfterStore($instance): void
    {}

    public function processAfterUpdate($oldInstance, $instance): void
    {}

    public function destroy($id)
    {
        $item = $this->modelClass::find($id);
        info('item inside delete');
        info($item);
        $name = ucfirst(Str::lower($this->getModelShortName()));
        if (!$this->authoriseDestroy($item)) {
            throw new AuthorizationException('Unable to delete the '.$name.'. The user is not authorised for this action.');
        }
        return $item->delete();
    }

    private function querySelectedIds(Builder $query, string $idKey, array $ids): void
    {
        $query->whereIn($idKey, $ids);
    }

    private function accessCheck(Model $item): bool
    {
        return true;
    }

    private function getSearchOperator($op, $val)
    {
        $ops = [
            'is' => 'like',
            'ct' => 'like',
            'st' => 'like',
            'en' => 'like',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
            'eq' => '=',
            'neq' => '<>',
        ];
        $v = $val;
        switch($op) {
            case 'ct':
                $v = '%'.$val.'%';
                break;
            case 'st':
                $v = $val.'%';
                break;
            case 'en':
                $v = '%'.$val;
                break;
        }
        // if (in_array($op, ['gt', 'lt', 'gte', 'lte','eq', 'neq'])) {
        //     $v = floatval($v);
        // }
        return [
            'op' => $ops[$op],
            'val' => $v
        ];
    }

    private function getSearchParams($query, array $searches, $searchesMap): array
    {
        $searchParams = [];
        foreach ($searches as $search) {
            $data = explode('::', $search);
            $rel = $searchesMap[$data[0]] ?? $data[0];
            // $rel = $data[0];
            $op = $this->getSearchOperator($data[1], $data[2]);
            // if($this->isRelation($rel)) {
            if($this->isRelation(explode('.', $rel)[0])) {
                $this->applyRelationSearch($query, $rel, $this->relations()[$rel]['search_column'], $op['op'], $op['val']);
            } else {
                $query->where($rel, $op['op'], $op['val']);
            }
            $searchParams[$rel] = $data[2];
        }
        // dd($searchesMap, $data[0],$searches, $searchParams);
        return $searchParams;
    }

    private function getFilterParams($query, array $filters, $filtersMap): array
    {
        $filterParams = [];
        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            $rel = $filtersMap[$data[0]] ?? $data[0];
            // $rel = $data[0];
            // dd($rel);
            $op = $this->getSearchOperator($data[1], $data[2]);
            // if($this->isRelation($rel)) {
            if($this->isRelation($rel)) {
                // dd($rel, $op['op'], $op['val']);
                $this->applyRelationSearch($query, $rel, $this->relations()[$rel]['filter_column'], $op['op'], $op['val']);
            } else {
                $query->where($rel, $op['op'], $op['val']);
            }
            $filterParams[$data[0]] = $data[2];
        }
        return $filterParams;
    }

    private function getSortParams($query, array $sorts, array $sortsMap): array
    {
        $sortParams = [];
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $key = $sortsMap[$data[0]] ?? $data[0];
            if (isset($usortkey) && isset($map[$data[0]])) {
                $type = $key['type'];
                $kname = $key['name'];
                switch ($type) {
                    case 'string';
                        $query->orderByRaw('CONCAT('.$kname.',\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'integer';
                        $query->orderByRaw('CONCAT(LPAD(ROUND('.$kname.',0),20,\'00\'),\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'float';
                        $query->orderByRaw('CONCAT( LPAD(ROUND('.$kname.',0) * 100,20,\'00\') ,\'::\','.$usortkey.') '.$data[1]);
                        break;
                    default:
                        $query->orderByRaw('CONCAT('.$kname.'\'::\','.$usortkey.') '.$data[1]);
                        break;
                }
            } else {
                $query->orderBy($data[0], $data[1]);
            }

            $sortParams[$data[0]] = $data[1];
        }
        // dd($sortParams);
        return $sortParams;
    }

    private function applyRelationSearch(Builder $query, $relName, $key, $op, $val): void
    {
        // If isset(search_fn): execute it
        if (isset($this->relations()[$relName]['search_fn'])) {
            $this->relations()[$relName]['search_fn']($query, $op['op'], $op['val']);
        } else {
            // Get relation type
            $type = $this->getRelationType($relName);
            // dd($type);
            switch ($type) {
                case 'BelongsTo':
                    $query->whereHas($relName, function ($q) use ($key, $op, $val) {
                        $q->where($key, $op, $val);
                    });
                    break;
                case 'HasOne':
                    $query->whereHas($relName, function ($q) use ($key, $op, $val) {
                        $q->where($key, $op, $val);
                    });
                    break;
                case 'HasMany':
                    $query->whereHas($relName, function ($q) use ($key, $op, $val) {
                        $q->where($key, $op, $val);
                    });
                    break;
                case 'BelongsToMany':
                    $query->whereHas($relName, function ($q) use ($key, $op, $val) {
                        $q->where($key, $op, $val);
                    });
                    break;
                default:
                    break;
            }
        }
    }

    private function getRelationType(string $relation): string
    {
        $obj = new $this->modelClass;
        $type = get_class($obj->{$relation}());
        $ar = explode('\\', $type);
        return $ar[count($ar) - 1];
    }

    private function getRelatedModelClass(string $relation): string
    {
        info('Model Class: XX - ' . $this->modelClass);
        $cl = $this->modelClass;
        $obj = new $cl;
        info(new $cl);
        $r = $obj->$relation();
        info($r->getRelated());
        return $r->getRelated();
    }

    private function isRelation($key): bool
    {
        return in_array($key, array_keys($this->relations()));
    }

    private function isMideaField($key): bool
    {
        return in_array($key, $this->getFileFields());
    }

    private function getFileFields(): array
    {
        return $this->mediaFields ?? [];
    }


    private function getPaginatorArray(LengthAwarePaginator $results): array
    {
        $data = $results->toArray();
        return [
            'currentPage' => $data['current_page'],
            'totalItems' => $data['total'],
            'lastPage' => $data['last_page'],
            'itemsPerPage' => $results->perPage(),
            'nextPageUrl' => $results->nextPageUrl(),
            'prevPageUrl' => $results->previousPageUrl(),
            'elements' => $results->links()['elements'],
            'firstItem' => $results->firstItem(),
            'lastItem' => $results->lastItem(),
            'count' => $results->count(),
        ];
    }

    protected function relations(): array
    {
        return [
            // 'relation_name' => [
            //     'type' => '',
            //     'field' => '',
            //     'search_fn' => function ($query, $op, $val) {}, // function to be executed on search
            //     'search_scope' => '', //optional: required only for combined fields search
            //     'sort_scope' => '', //optional: required only for combined fields sort
            //     'models' => '' //optional: required only for morph types of relations
            // ],
        ];
    }

    // protected function extraConditions(Builder $query): void {}
    protected function applyGroupings(Builder $q): void {}

    protected function formatIndexResults(array $results): array
    {
        return $results;
    }

    protected function preIndexExtra(): void {}
    protected function postIndexExtra(): void {}

    protected function getIndexHeaders(): array
    {
        return [];
    }

    protected function getIndexColumns(): array
    {
        return [];
    }

    protected function getAdvanceSearchFields(): array
    {
        return [];
    }

    protected function getPageTitle(): string
    {
        return Str::headline(Str::plural($this->getModelShortName()));
    }

    protected function getSelectedIdsUrl(): string
    {
        return route(Str::lower(Str::plural($this->getModelShortName())).'.selectIds');
    }

    protected function getDownloadUrl(): string
    {
        return route(Str::lower(Str::plural($this->getModelShortName())).'.download');
    }

    protected function getCreateRoute(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'.create';
    }

    protected function getViewRoute(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'.view';
    }

    protected function getEditRoute(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'.edit';
    }

    protected function getDestroyRoute(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'.destroy';
    }

    protected function getIndexId(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'_index';
    }

    public function getDownloadCols(): array
    {
        return [];
    }

    public function getDownloadColTitles(): array
    {
        return [];
    }

    public function getCreatePageData(): CreatePageData
    {
        $sn = $this->getModelShortName();
        return new CreatePageData(
            title: Str::plural($sn),
            form: FormHelper::makeForm(
                title: 'Create '.$sn,
                id: 'form_'.Str::lower(Str::plural($sn)).'_create',
                action_route: Str::lower(Str::plural($sn)) . '.store',
                success_redirect_route: Str::lower(Str::plural($sn)). '.show',
                success_redirect_key: 'id',
                cancel_route: 'dashboard',
                items: $this->getCreateFormElements(),
                layout: $this->buildCreateFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform',
            )
        );
    }

    public function getEditPageData($keyCol = 'id', $keyVal = null): array
    {
        $instance = $this->modelClass::where($keyCol, $keyVal)
            ->get()->first();
        $sn = $this->getModelShortName();
        return [
            'title' => Str::plural($sn),
            'form' => FormHelper::makeForm(
                title: 'Edit '.$sn,
                id: 'form_'.Str::lower(Str::plural($sn)).'_edit',
                action_route: Str::lower(Str::plural($sn)) . '.store',
                success_redirect_route: Str::lower(Str::plural($sn)). '.show',
                success_redirect_key: 'id',
                cancel_route: 'dashboard',
                items: $this->getEditFormElements(),
                layout: $this->buildEditFormLayout(),
                label_position: 'top',
                width: 'full',
                type: 'easyadmin::partials.simpleform',
            ),
            '_old' => $instance
        ];
    }

    private function formElements(Model $instance = null): array
    {
        return [];
    }

    public function buildCreateFormLayout(): array
    {
        return [];
    }

    public function buildEditFormLayout(): array
    {
        return [];
    }

    public function getStoreValidationRules(): array
    {
        return $this->storeValidationRules ?? [];
    }

    public function getUpdateValidationRules(): array
    {
        return $this->updateValidationRules ?? [];
    }

    public function suggestList($search = null)
    {
        if (isset($search)) {
            switch($this->defaultSearchMode) {
                case 'contains':
                    $search = '%'.$search.'%';
                    break;
                case 'startswith':
                    $search = $search.'%';
                    break;
                case 'endswith':
                    $search = '%'.$search;
                    break;
            }
            return new InputUpdateResponse(
                result: $this->modelClass::where($this->defaultSearchColumn, 'like', $search)->get(),
                message: 'ok',
                isvalid: true
            );
            return $this->modelClass::where($this->defaultSearchColumn, 'like', $search)->get();
        } else {
            return new InputUpdateResponse(
                result: $this->modelClass::all(),
                message: 'ok',
                isvalid: true
            );
        }
    }

    public function getModelShortName() {
        $a = explode('\\', $this->modelClass);
        return $a[count($a) - 1];
    }

    private function processBeforeStore(array $data): array
    {
        return $data;
    }

    public function processBeforeUpdate(array $data, $id = null): array
    {
        return $data;
    }

    public function prepareForStoreValidation(array $data): array
    {
        return $data;
    }

    public function prepareForUpdateValidation(array $data): array
    {
        return $data;
    }

    public function processRelationsAfterStore(Model $instance, array $relations)
    {}

    public function processMediaAfterStore(Model $instance, array $relations)
    {}

    public function processRelationsAfterUpdate(Model $instance, array $relations)
    {}

    public function processMediaAfterUpdate(Model $instance, array $relations)
    {}

    public function getCreateFormElements(): array
    {
        $t = [];
        foreach ($this->formElements() as $key => $el) {
            if (!isset($el['form_types']) || in_array('create', $el['form_types'])) {
                $t[$key] = $el;
            }
        }
        return $t;
    }

    public function getEditFormElements($model = null): array
    {
        $t = [];
        foreach ($this->formElements($model) as $key => $el) {
            if (!isset($el['form_types']) || in_array('edit', $el['form_types'])) {
                $t[$key] = $el;
            }
        }
        return $t;
    }

    public function authoriseIndex(): bool
    {
        return true;
    }

    public function authoriseShow($item): bool
    {
        return true;
    }

    public function authoriseCreate(): bool
    {
        return true;
    }

    public function authoriseStore(): bool
    {
        return true;
    }

    public function authoriseEdit($id): bool
    {
        return true;
    }

    public function authoriseUpdate($item): bool
    {
        return true;
    }

    public function authoriseDestroy($item): bool
    {
        return true;
    }

    private function getSelectionEnabled(): bool
    {
        return $this->selectionEnabled ?? true;
    }

    private function getExportsEnabled(): bool
    {
        return $this->exportsEnabled ?? true;
    }

    private function getshowAddButton(): bool
    {
        return $this->showAddButton ?? true;
    }
}
?>
