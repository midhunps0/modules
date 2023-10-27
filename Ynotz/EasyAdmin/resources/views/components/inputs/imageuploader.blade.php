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
    $allow_gallery = $element['allow_gallery'] ?? false;
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
    // dd($existing_values);
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
        showGallery: false,
        galleryItems: [],
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
            console.log('size validation: '+file.size + ' < ' + this.validations.maxSize);
            if(file.size > sizeBytes) {
                return false;
            }

            return true;
        },
        doUpload(files) {
            if (!this.multiple) { this.files = []; }
            for(i = 0; i < this.inputElement.files.length; i++) {
                file = this.inputElement.files[i];
                let isValidSize = this.validateSize(file);
                newFile = {
                    file: file,
                    name: file.name,
                    uploaded_pc: 0,
                    id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                    servername: '',
                    url: '',
                    show: true,
                    fromServer: false,
                    sizeValidation: isValidSize,
                    typeValidation: this.validateType(file),
                    error: false
                };
                if (!isValidSize) {
                    this.compressAndUpload(newFile);
                } else {
                    this.files.push(newFile);
                    this.upoladFile(newFile);
                }
                // validate size
                // validate type
            }
            this.inputElement.value = '';
            {{-- if (this.inputElement != null) { this.inputElement.value = ''; } --}}
        },
        compressAndUpload(img) {
            console.log(img.file);
            new window.Compressor(img.file, {
                quality: 0.6,
                maxWidth: 750,
                maxHeight: 400,

                // The compression process is asynchronous,
                // which means you have to access the `result` in the `success` hook function.
                success: (result) => {
                    console.log('result file');
                    console.log(result);
                    img.file = result;
                    console.log(img.file);
                    img.sizeValidation = this.validateSize(img.file);
                    this.files.push(img);
                    this.upoladFile(img);
                },
                error(err) {
                  console.log(err.message);
                },
              });
            return img;
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
                    if (f.id == file.id) {
                        f.servername = r.data.name;
                        f.url = r.data.url;
                    }
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
                            this.files = this.files.filter((f) => {
                                return f.id != id;
                            });
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
                    let theFile = this.files.filter((f) => {
                        return f.id == id;
                    })[0];
                    if (theFile.servername.includes(this.ulidSeparator)) {
                        this.galleryItems.forEach((i) => {
                            if (i.ulid == theFile.servername.replace(this.ulidSeparator, '')) {
                                i.selected = false;
                            }
                        });
                    }
                    this.files = this.files.filter((f) => {
                        return f.id != id;
                    });

                }
            }
        },
        fetchGalleryItems() {
            if (this.galleryItems.length == 0) {
                let url = '{{route(config('mediaManager.gallery_route'))}}';
                console.log('url: '+ url);
                axios.get(
                    url
                ).then((r) => {
                    this.galleryItems = r.data.items.map((i) => {
                        let x = i;
                        i.selected = false;
                        return i;
                    });
                }).catch((e) => {
                    console.log(e);
                });
            }
        },
        openGallery() {
            this.fetchGalleryItems();
            this.showGallery = true;
        },
        closeGallery() {
            this.showGallery = false;
        },
        galleryClick(ulid) {
            let item = this.galleryItems.filter((i) => {
                return i.ulid == ulid;
            })[0];
            if (item.selected) {
                this.deselectGalleryItem(item);
            } else {
                this.selectGalleryItem(item);
            }
        },
        selectGalleryItem(item) {
            item.selected = true;
            console.log('item');
            console.log(item);
            let check = this.files.filter((f) => {
                return f.servername == config('mediaManager.ulid_separator')+item.ulid;
            });
            if (check.length == 0) {
                let newFile = {
                    file: null,
                    name: item.name,
                    uploaded_pc: 100,
                    id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                    servername: this.ulidSeparator+item.ulid,
                    url: item.url,
                    show: true,
                    fromServer: true,
                    sizeValidation: true,
                    typeValidation: true,
                    error: false
                };
                this.files.push(newFile);
            }
        },
        deselectGalleryItem(item) {
            item.selected = false;
            this.files = this.files.filter((f) => {
                return f.servername != this.ulidSeparator+item.ulid;
            });
        },
        resetOnEvent(detail) {
            if(this.resetSources.includes(detail.source)) {
                this.reset();
            }
        },
        reset() {
            this.inputElement.value = '';
            this.files = [];
            this.galleryItems = [];
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
                files.push({
                    file: null,
                    name: ('{{$path}}'.split('/')).pop(),
                    uploaded_pc: 100,
                    id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                    servername: ulidSeparator+'{{$ulid}}',
                    url: '{{$path}}',
                    show: true,
                    fromServer: true,
                    error: false
                });
            @endforeach
            @else
                files.push({
                    file: null,
                    name: ('{{$existing_values['path']}}'.split('/')).pop(),
                    uploaded_pc: 100,
                    id: (new Date()).getTime() + Math.floor(Math.random() * 100),
                    servername: ulidSeparator+'{{$existing_values['ulid']}}',
                    url: '{{$existing_values['path']}}',
                    show: true,
                    fromServer: true,
                    error: false
                });
            @endif
        @endif
        @if (!is_array($xerrors) && $xerrors->has($name))
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
    x-show="showelement"
>
    <div
        @class([
            'relative',
            'form-control',
            '{{$wclass}}',
            'my-6 flex flex-row' => $label_position == 'side'
        ])>
        @if ($label_position != 'float')
        <label @class([
                'px-2 label',
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
                        <label class="px-2 relative">
                            <span class="label-text">{{$label}}</span>@if (isset($properties['required']) && $properties['required'])
                            &nbsp;<span class="text-warning">*</span>@endif
                        </label>
                    @endif
                    <div class="relative">
                        <button type="button" tabindex="0" x-show="files.length == 0 || multiple == true" @click="initFilepicker()" class="h-12 w-32 border border-base-content border-opacity-30 border-dotted flex flex-row justify-center items-center" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                            <span class="opacity-30"><x-easyadmin::display.icon icon="easyadmin::icons.plus"/></span>&nbsp;<span class="text-xs opacity-30">Upload</span>
                        </button><div x-show="files.length > 0" class="text-warning text-xs"><span x-text="files.length"></span> image<span x-show="files.length > 1">s</span> selected</div>
                        <input type="file" id="{{$elid}}" class="h-1 absolute -z-10 left-0" @if (isset($properties['multiple']) && $properties['multiple']) multiple @endif :required="required && files.length == 0" :accept="validations.mimeTypes.join(', ')"
                            @change="doUpload()"
                            >
                    </div>
                    @if ($allow_gallery)
                        <button type="button" tabindex="0" x-show="files.length == 0 || multiple == true" @click.prevent.stop="openGallery()" class="h-12 w-32 border border-base-content border-opacity-30 border-dotted flex flex-row justify-center items-center" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-md'">
                            <span class="opacity-30"><x-easyadmin::display.icon icon="easyadmin::icons.cloud_up"/></span>&nbsp;<span class="text-xs opacity-30">From gallery</span>
                        </button>
                    @endif
                </div>
                <div class="w-11/12 overflow-x-auto flex flex-col space-y-2 max-h-60">
                    <template x-for="file in files">
                        <div x-show="file.show == true" x-transition.dutation.100ms
                        {{-- class="px-2 py-2 space-x-4 flex flex-row border border-base-content border-opacity-20 items-center" :class="theme == 'rounded' ? 'rounded-md' : ''" --}}
                        >
                            <div :class="{
                                'relative px-2 py-2 space-x-4 flex flex-row justify-start items-center mb-2': true,
                                'rounded-full': theme == 'rounded',
                                'rounded-md': theme != 'rounded',
                            }">
                                <div class="relative flex flex-row space-x-2 items-center">
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
                                    <div class="w-16 h-16 flex flex-row items-center justify-center border-2 border-base-content border-opacity-10 overflow-hidden"
                                        :class="{
                                            'bg-error bg-opacity-30 border-4 border-error border-opacity-40': !file.fromServer && (file.error || !file.sizeValidation || !file.typeValidation),
                                            'bg-base-200': !file.error,
                                            'rounded-full': theme == 'rounded',
                                            'rounded-md': theme != 'rounded',
                                        }">
                                        <img :src="file.url" class="h-16" :class="{
                                            'mask mask-circle': theme == 'rounded',
                                            'rounded-md': theme != 'rounded',
                                            'opacity-40': !file.fromServer && (file.error || !file.sizeValidation || !file.typeValidation),
                                            }">
                                    </div>
                                </div>
                                <span class="flex flex-row items-center justify-start text-xs" x-text="file.name"></span>
                                <button type="button" tabindex="0" x-show="file.uploaded_pc == 100 || !file.sizeValidated || !file.typeValidated" x-transition class="border border-warning border-opacity-50 p-0 h-6 w-6 flex flex-row items-center justify-center text-error opacity-70" :class="theme == 'rounded' ? 'rounded-full' : 'rounded-sm'" @click.prevent.stop="removeSelectedFile(file.id);">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
                                </button>

                            </div>
                            <div class="flex flex-row justify-start">
                                <span x-show="!file.sizeValidation && !file.fromServer" class="text-xs italic text-warning opacity-50">Exceeds size limit. &nbsp;&nbsp;</span>
                                <span x-show="!file.typeValidation && !file.fromServer" class="text-xs italic text-warning opacity-50">Invalid type.</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        <div class="text-xs text-warning opacity-80" x-text="validationsString"></div>
        <x:easyadmin::partials.errortext />
    </div>


    <div class="fixed top-0 left-0 z-50 w-screen h-screen bg-base-300 bg-opacity-50 flex flex-col justify-evenly" x-show="showGallery" x-transition>
        <div class="mx-auto w-4/5 sm:w-4/5 md:w-2/3 min-h-2/3 max-h-4/5 p-0 shadow-md border-base-300 bg-opacity-100 text-center flex flex-col items-center rounded-md overflow-hidden space-y-0">
            <div class="relative p-2 bg-base-200 w-full shadow-md text-opacity-10">Choose From Gallery
                <button type="button" @click.prevent.stop="closeGallery()" class="absolute top-2 right-2 block rounded-sm border border-error border-opacity-50 text-error opacity-50">
                    <x-easyadmin::display.icon icon="easyadmin::icons.close" width="w-5" height="h-5" />
                </button>
            </div>
            <div class="relative p-2 border-4 border-base-200 flex-grow w-full bg-base-100 rounded-md flex flex-col space-x-2 space-y-2">
                <div x-show="galleryItems.length == 0" class="w-full h-full flex flex-row justify-center items-center">
                    <div class="flex flex-row items-center justify-center">
                        <x-easyadmin::display.icon class="animate-spin" icon="easyadmin::icons.at_symbol"/>
                        &nbsp;
                        <span class="animate-pulse">loading...</span>
                    </div>
                </div>
                <div class="w-full h-full flex flex-row flex-wrap items-start space-x-2 space-y-2">
                    <template x-for="item in galleryItems">
                        <label @click.prevent.stop="galleryClick(item.ulid)" class="relative rounded-md h-32 w-28 border border-base-content border-opacity-20 flex flex-col overflow-hidden" :class="!item.selected || 'bg-primary'">
                            <div class="w-24 h-24 m-2 flex flex-row justify-center items-center">
                                <img :src="item.url" class="max-h-24 max-w-24 mask">
                            </div>
                            <p x-text="'..'+item.filename.split('').reverse().join('').substring(0, 12).split('').reverse().join('')" class="text-left text-xs w-full bg-base-200 p-1"></p>
                        </label>
                    </template>
                </div>
                <div class="w-full text-center p-2 mt-4">
                    <button type="button" class="btn btn-sm btn-warning min-w-16" @click.prevent.stop="closeGallery()">Ok</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endif
