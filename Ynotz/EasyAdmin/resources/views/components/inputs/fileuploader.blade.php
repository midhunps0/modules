@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top',
])
@php
    $name = $element['key'];
    $authorised = $element['authorised'];
    $label = $element['label'];
    $validations = $element['validations'];
    $width = $element['width'] ?? 'full';
    $placeholder = $element["placeholder"] ?? null;
    $wrapper_styles = $element["wrapper_styles"] ?? null;
    $input_styles = $element["input_styles"] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $theme = $element['theme'] ?? 'regular';
    $show = $element['show'] ?? true;

    $wclass = 'w-full';
    switch ($width) {
        case 'full':
            $wclass = 'w-full';
            break;
        case '1/2':
            $wclass = 'w-1/2';
            break;
        case '1/3':
            $wclass = 'w-1/3';
            break;
        case '2/3':
            $wclass = 'w-2/3';
            break;
        case '1/4':
            $wclass = 'w-1/4';
            break;
        case '3/4':
            $wclass = 'w-3/4';
            break;
    }
    if ($label_position == 'side') {
        $wclass .= ' my-6 flex flex-row';
    } else {
        $wclass .= ' my-4';
    }
    $elid = Illuminate\Support\Str::ulid();
    $existing_values = null;

    if (isset($_current_values[$name])) {
        $existing_values = $_current_values[$name];
    } elseif (isset($_old[$name])) {
        $existing_values = $_old[$name];
    }
@endphp
@if ($authorised)
    <div x-data="
        {
            url: '{{route('mediamanager.file_upload')}}',
            deleteUrl: '{{route('mediamanager.file_delete')}}',
            inputElement: null,
            files: [],
            invalidatedFiles: [],
            theme: 'regular',
            allowFromGallery: false,
            multiple: false,
            required: false,
            requiredFlag: false,
            {{-- mimeTypes: [],
            maxSize: null, --}}
            showConfirm: false,
            deleteItemKey: null,
            errors: '',
            validations: [],
            fireInputEvent: false,
            resetSources: [],
            toggleListeners: {},
            showelement: true,
            ulidSeparator: '_ulid::',
            get validationsString() {
                let str = '';
                if (typeof this.validations.maxSize != 'undefined' && this.validations.maxSize != null) {
                    str += 'Maximum size: ' + this.validations.maxSize + '.';
                }
                if (typeof this.validations.mimeTypes != 'undefined' && this.validations.mimeTypes != null) {
                    let mt = this.validations.mimeTypes.reduce((result, m) => {
                        return result.length == 0 ? result + m : result + ', ' + m;
                    }, '');

                    return mt.length > 0 ? (str + ' Allowed types: ' + mt).trim() : str.trim();
                }
                console.log('str: '+ str);
                return str;
            },
            initFilepicker() {
                if (!this.allowFromGallery) {
                    this.inputElement.click();
                } else {
                    alert('implement file chooser modal');
                }
            },
            validateType(file) {
                if(this.validations.mimeTypes != undefined && this.validations.mimeTypes != null && this.validations.mimeTypes.length > 0 && this.validations.mimeTypes.indexOf(file.type) == -1) {
                    return false;
                }
                return true;
            },
            validateSize(file) {
                if (typeof this.validations.maxSize == 'undefined' || this.validations.maxSize == null) {
                    return true;
                }
                let sizeArr = this.validations.maxSize.split(' ');
                let size = sizeArr[0];
                let unit = sizeArr[1];
                let sizeBytes = size;
                switch(unit.toLowerCase()) {
                    case 'gb':
                        sizeBytes = size * 1024 * 1024 * 1024;
                        break;
                    case 'mb':
                        sizeBytes = size * 1024 * 1024;
                        break;
                    case 'kb':
                        sizeBytes = size * 1024;
                        break;
                }
                if(file.size > sizeBytes) {
                    return false;
                }
                console.log(file.size + ' < ' + this.validations.maxSize);
                return true;
            },
            doUpload(files) {
                if (!this.multiple) { this.files = []; }
                for(i = 0; i < this.inputElement.files.length; i++) {
                    file = this.inputElement.files[i];
                    // validate size
                    // validate type
                    newFile = {
                        file: file,
                        name: file.name,
                        uploaded_pc: 0,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        servername: '',
                        show: true,
                        fromServer: false,
                        sizeValidation: this.validateSize(file),
                        typeValidation:this.validateType(file),
                        error: false
                    };
                    this.files.push(newFile);
                    this.upoladFile(newFile);
                }
                this.inputElement.value = '';
                {{-- if (this.inputElement != null) { this.inputElement.value = ''; } --}}
            },
            upoladFile(file) {
                let formData = new FormData();
                formData.append('file', file.file);
                axios.post(
                    this.url,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        onUploadProgress: (event) => {
                            let pc = Math.floor((event.loaded * 100) / event.total);
                            this.files.forEach((f) => {
                                if (f.id == file.id) { f.uploaded_pc = pc; }
                            });
                        },
                    }
                ).then((r) => {
                    file.uploaded_pc = 100;
                    this.files.forEach((f) => {
                        if (f.id == file.id) { f.servername = r.data.name; }
                    });
                }).catch((e) => {
                    console.log(e);
                });
            },
            doRemove(id) {
                let theFile = this.files.filter((f) => {
                    return f.id == id;
                })[0];
                let formData = new FormData();
                formData.append('_method', 'delete');
                formData.append('file', theFile.servername);
                axios.post(
                    this.deleteUrl,
                    formData,
                        {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                ).then((r) => {
                    if (r.data.success) {
                        this.files.forEach((f) => {
                            if (f.id == id) {
                                f.show = false;
                                setTimeout(() => {
                                    this.files = this.files.filter((f) => {
                                        return f.id != id;
                                    });
                                }, 100);
                            }
                        });
                        this.deleteItemKey = null;
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            removeSelectedFile(id, isValidated = true) {
                if (!isValidated) {
                    this.invalidatedFiles = this.invalidatedFiles.filter((f) => {
                        return f.id!== id;
                    });
                } else {
                    let file = this.files.filter((f) => {
                        return f.id == id;
                    })[0];
                    if (!file.fromServer) {
                        this.doRemove(id);
                    } else {
                        {{-- this.deleteItemKey = id;
                        this.showConfirm = true; --}}
                        this.files = this.files.filter((f) => {
                            return f.id!== id;
                        });
                    }
                }
            },
            confirmDelete() {
                this.doRemove(this.deleteItemKey);
            },
            cancelDelete(id) {},
            resetOnEvent(detail) {
                if(this.resetSources.includes(detail.source)) {
                    this.reset();
                }
            },
            reset() {
                this.inputElement.value = '';
                this.files = [];
            },
            toggleOnEvent(source, value) {
                if (Object.keys(this.toggleListeners).includes(source)) {
                    this.toggleListeners[source].forEach((item) => {
                        switch(item.condition) {
                            case '==':
                                if (item.value == value) {
                                    this.showelement = item.show;
                                }
                                break;
                            case '!=':
                                if (item.value != value) {
                                    this.showelement = item.show;
                                }
                                break;
                            case '>':
                                if (item.value > value) {
                                    this.showelement = item.show;
                                }
                                break;
                            case '<':
                                if (item.value < value) {
                                    this.showelement = item.show;
                                }
                                break;
                            case '>=':
                                if (item.value >= value) {
                                    this.showelement = item.show;
                                }
                                break;
                            case '<=':
                                if (item.value <= value) {
                                    this.showelement = item.show;
                                }
                                break;
                        }
                    });
                }
            }
        }
        "
        x-init="
            ulidSeparator = '{{config('mediaManager.ulid_separator')}}';
            @if (isset($element['url']))
                url = '{{ $element['url'] }}';
            @endif
            @if (isset($element['delete_url']))
                deleteUrl = '{{ $element['delete_url'] }}';
            @endif
            inputElement = document.getElementById('{{$elid}}');
            @if (isset($validations) && count($validations) > 0)
                @if (isset($validations['mime_types']))
                    validations.mimeTypes = [
                        @foreach ($validations['mime_types'] as $type)
                            '{{$type}}',
                        @endforeach
                    ];
                @endif
                @if (isset($validations['max_size']))
                    validations.maxSize = '{{$validations['max_size']}}';
                @endif
                console.log('validations');
                console.log(validations);
                console.log(validationsString);
            @endif
            @if (isset($properties['multiple']) && $properties['multiple'])
                multiple = true;
            @endif
            @if (isset($existing_values))
                @if(isset($properties['multiple']) && $properties['multiple'])
                @foreach ($existing_values as $path => $ulid)
                    this.files.push({
                        file: null,
                        name: ('{{$path}}'.split('/')).pop(),
                        uploaded_pc: 100,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        servername: 'ulidSeparator'+'{{$ulid}}',
                        url: '{{$path}}',
                        show: true,
                        fromServer: true,
                        error: false
                    });
                @endforeach
                @else
                    this.files.push({
                        file: null,
                        name: ('{{$existing_values[$path]}}'.split('/')).pop(),
                        uploaded_pc: 100,
                        id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                        servername: 'ulidSeparator'+'{{$existing_values[$ulid]}}',
                        url: '{{$path}}',
                        show: true,
                        fromServer: true,
                        error: false
                    });
                @endif
            @endif
            @if ($xerrors->has($name))
                ers = {{json_encode($xerrors->get($name))}};
                console.log(ers);
                errors = ers.reduce((r, e) => {
                    return r + ' ' + e;
                }, '').trim();
            @endif
            @if (isset($properties['required']) && $properties['required'])
                requiredFlag = true;
                required = files.length == 0;
            @endif
            @if (isset($theme))
                theme = '{{$theme}}';
            @endif

            @if($fire_input_event)
                fireInputEvent = true;
            @endif

            @if (isset($reset_on_events))
                @foreach ($reset_on_events as $source)
                    resetSources.push('{{$source}}');
                @endforeach
            @endif

            @if (isset($toggle_on_events))
            @foreach ($toggle_on_events as $source => $conditions)
                toggleListeners.{{$source}} = [];
                @foreach ($conditions as $condition)
                    toggleListeners.{{$source}}.push({
                        condition: '{{$condition[0]}}',
                        value: '{{$condition[1]}}',
                        show: {{$condition[2] ? 'true' : 'false'}},
                    });
                @endforeach
            @endforeach
            @endif

            {{-- To manage the required feature when loading old values in edit --}}
            $watch('files', (f) => {
                if (f.length == 0 && requiredFlag) {
                    required = true;
                }
            });
        "
        @if (isset($reset_on_events) && count($reset_on_events) > 0)
        @eaforminputevent.window="resetOnEvent($event.detail);"
        @endif
        @if (isset($toggle_on_events) && count($toggle_on_events) > 0)
        @eaforminputevent.window="toggleOnEvent($event.detail.source, $event.detail.value);"
        @endif
        @formerrors.window="
            errors = '';

            erArr = [];
            Object.keys($event.detail.errors).forEach((e) => {
                if (e.startsWith('{{$name}}')) {
                    erArr.push(($event.detail.errors[e]).reduce((result, e) => {
                        if (result.length > 0) {
                            return result + ' ' + e;
                        } else {
                            return result + e;
                        }
                    }));
                }
            });

            errors = erArr.reduce(
                (result, e) => {
                    fileKey = e.split('::')[0];
                    theFile = files.filter((f) => {
                        return f.servername == fileKey;
                    })[0];
                    theFile.error = true;
                    if (result.length > 0) {
                        return result + ' ' + e.replace(fileKey+'::', theFile.name);
                    } else {
                        return result + e.replace(fileKey+'::', theFile.name);
                    }
                },
                ''
            );
            if (errors.lengt == 0) {
                inputElement.value = '';
            }
            {{-- } else {
                errors = '';
            } --}}
            "
        >
        <div
            @class([
                'relative',
                'form-control',
                $wclass,
                'flex flex-row' => $label_position == 'side'
            ])>
        @if ($label_position != 'float')
            <label @class([
                    'label',
                    'justify-start',
                    'w-36' => $label_position == 'side'
                ])>
                <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                &nbsp;<span class="text-warning">*</span>@endif
            </label>
        @endif
        <template x-for="file in files">
            <input type="hidden" @if (isset($properties['multiple']) && $properties['multiple']) name="{{$name}}[]" @else name="{{$name}}" @endif x-model="file.servername">
        </template>
        <div class="@if ($label_position == 'side') flex-grow @else w-full @endif">
        <div class="border border-base-content border-opacity-20 rounded-lg bg-base-100 flex flex-col items-start space-y-2 relative p-2 space-x-2">
            <div class="flex flex-row space-x-2">
                @if ($label_position == 'float')
                    <label class="relative">
                        <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                        &nbsp;<span class="text-warning">*</span>@endif
                    </label>
                @endif
                <div class="relative">
                    <button type="button" tabindex="0" x-show="files.length == 0 || multiple == true" @click="initFilepicker()" class="h-12 w-32 border border-base-content border-opacity-30 border-dotted flex flex-row justify-center items-center" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                        <span class="opacity-30"><x-easyadmin::display.icon icon="easyadmin::icons.plus"/></span>
                    </button><div x-show="files.length > 0" class="text-warning text-xs"><span x-text="files.length"></span> file<span x-show="files.length > 1">s</span> selected</div>
                    <input type="file" id="{{$elid}}" class="h-1 absolute -z-10 left-0" @if (isset($properties['multiple']) && $properties['multiple']) multiple @endif :required="required" :accept="validations.mimeTypes.join(', ')"
                        @change="doUpload()"
                        >
                </div>
            </div>
            <div class="w-11/12 overflow-x-auto flex flex-col space-y-2">
                <template x-for="file in files">
                    <div x-show="file.show == true" x-transition.dutation.100ms
                    {{-- class="px-2 py-2 space-x-4 flex flex-row border border-base-content border-opacity-20 items-center" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md', file.error ? 'bg-error bg-opacity-30' : 'bg-base-200'" --}}
                    >
                        <div :class="{
                            'px-2 py-2 space-x-4 flex flex-row justify-between border border-base-content border-opacity-20 items-center mb-2': true,
                            'bg-error bg-opacity-30': file.error || !file.sizeValidation || !file.typeValidation,
                            'bg-base-200': !file.error,
                            'rounded-full': theme == 'rounded',
                            'rounded-md': theme != 'rounded'
                        }">
                            <div class="flex flex-row space-x-2 items-center">
                                <div x-show="file.sizeValidation && file.typeValidation || file.fromServer" class="radial-progress text-xs" :class="file.uploaded_pc == 100 ? 'text-success' : 'text-base-content opacity-30'"
                                {{-- style="--size:0.2rem; --thickness: 2px;" --}}
                                :style="'--value:'+file.uploaded_pc+'; --size: 1.5rem; --thickness: 2px;'">
                                    <span x-show="!file.fromServer && file.uploaded_pc == 100" x-transition><x-easyadmin::display.icon icon="easyadmin::icons.tick" height="h-3" width="w-3"/></span>
                                    <span x-show="file.fromServer" x-transition><x-easyadmin::display.icon icon="easyadmin::icons.cloud_up"  height="h-3" width="w-3"/></span>
                                </div>

                                {{-- <div x-show="!file.fromServer && file.uploaded_pc != 100" class="radial-progress text-xs text-base-content opacity-30" style="--size:0.8rem; --thickness: 2px;" :style="'--value:'+file.uploaded_pc+'; --size: 2rem; --thickness: 2px;'">
                                </div> --}}

                                {{-- <div x-show="file.fromServer" class="radial-progress text-xs text-success" style="--size:0.8rem; --thickness: 2px;" :style="'--value:'+file.uploaded_pc+'; --size: 2rem; --thickness: 2px;'">
                                    <span x-transition><x-easyadmin::display.icon icon="easyadmin::icons.cloud_up" /></span>
                                </div> --}}


                                <span class="flex flex-row items-center justify-start" x-text="file.name"></span>
                            </div>

                            <button type="button" tabindex="0" x-show="file.uploaded_pc == 100 || !file.sizeValidated || !file.typeValidated" x-transition class="border border-warning border-opacity-50 p-0 h-6 w-6 flex flex-row items-center justify-center text-error opacity-70" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-sm'" @click.prevent.stop="removeSelectedFile(file.id);">
                                <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
                            </button>
                        </div>
                        <div class="flex flex-row justify-start">
                            <span x-show="!file.sizeValidation" class="text-xs italic text-warning opacity-50">Exceeds size limit. &nbsp;&nbsp;</span>
                            <span x-show="!file.typeValidation" class="text-xs italic text-warning opacity-50">Invalid type.</span>
                        </div>
                    </div>
                </template>
            </div>
            {{-- <template x-for="file in invalidatedFiles">
                <div x-show="file.show == true" x-transition.dutation.500ms class="px-2 py-2 space-x-4 flex flex-row border border-error border-opacity-50 items-center bg-error bg-opacity-30" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                    <span class="flex flex-row items-center justify-start" x-text="file.name"></span>
                    <button type="button" tabindex="0" x-transition class="border border-warning border-opacity-50 p-0 h-6 w-6 flex flex-row items-center justify-center text-error opacity-70" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-sm'" @click.prevent.stop="removeSelectedFile(file.id, false);">
                        <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
                    </button>
                </div>
            </template> --}}
        </div>
        <div class="text-xs text-warning opacity-80" x-text="validationsString"></div>
        <x:easyadmin::partials.errortext />
        </div>
    </div>
        {{-- <x-easyadmin::display.confirm message="The file will be deleted from the server. Do you want to coninue?" showKey="showConfirm" :okFunction="'confirmDelete'" :cancelFunction="'cancelDelete'"/> --}}
    </div>
@endif
