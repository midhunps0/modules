<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class FormHelper
{
    /**
     * Undocumented function
     *
     * @param string $title Title of the form
     * @param string $id Id of the form element in html
     * @param string $action_route Action attribute of the form. The route which handles form submission.
     * @param string $cancel_route The route to redirect to upon clicking cancel button.
     * @param string $cancel_route_key The route parameter for cancel_route.
     * @param string $success_redirect_route The route to which the page shall be redirected after successful form submission
     * @param string $success_redirect_key The route parameter for the success route, if any
     * @param array $items Array of form elements obtained by calling getCreateFormElememts() or getEditFormElements() methods of the connector service class.
     * @param string $label_position The position of the labels of form elements. Can be top, side or float
     * @param string $type The form type (blade component) to be used. Default: simpleform
     * @param string $method The action method.
     * @return array
     */
    public static function makeForm(
        string $title,
        string $id,
        string $action_route,
        string $success_redirect_route,
        array $items,
        string $success_redirect_key = null,
        array $layout = null,
        array $action_route_params = [],
        string $cancel_route = null,
        string $cancel_route_key = null,
        string $label_position = 'float',
        string $type = 'easyadmin::partials.simpleform',
        string $width = '1/2',
        $submitLabel = 'Submit',
        $cancelLabel = 'Cancel',
        string $method = "POST",
    ): array
    {
        return [
            'title' => $title,
            'id' => $id,
            'action_route' => $action_route,
            'action_route_params' => $action_route_params,
            'cancel_route' => $cancel_route,
            'cancel_route_key' => $cancel_route_key,
            'success_redirect_route' => $success_redirect_route,
            'success_redirect_key' => $success_redirect_key,
            'items' => $items,
            'layout' => $layout,
            'label_position' => $label_position,
            'type' => $type,
            'width' => $width,
            'submit_label' => $submitLabel,
            'cancel_label' => $cancelLabel,
            'method' => $method
        ];
    }

    /**
     * Undocumented function
     *
     * @param string $title Title of the form
     * @param string $id Id of the form element in html
     * @param string $action_route Action attribute of the form. The route which handles form submission.
     * @param string $cancel_route The route to redirect to upon clicking cancel button.
     * @param string $success_redirect_route The route to which the page shall be redirected after successful form submission
     * @param array $items Array of form elements obtained by calling getCreateFormElememts() or getEditFormElements() methods of the connector service class.
     * @param string $label_position The position of the labels of form elements. Can be top, side or float
     * @param string $type The form type (blade component) to be used. Default: simpleform
     * @return array
     */
    public static function makeEditForm(
        string $title,
        string $id,
        string $action_route,
        string $success_redirect_route,
        array $items,
        array $layout = null,
        array $action_route_params = [],
        string $cancel_route = null,
        string $label_position = 'float',
        string $width = '1/2',
        string $type = 'easyadmin::partials.simpleform',
    ): array
    {
        return Self::makeForm(
            title: $title,
            id: $id,
            action_route: $action_route,
            success_redirect_route: $success_redirect_route,
            items: $items,
            layout: $layout,
            action_route_params: $action_route_params,
            cancel_route: $cancel_route,
            label_position: $label_position,
            width: $width,
            type: $type,
            method: "PUT"
        );
    }
    /**
     * function makeInput
     *
     * @param string $inputType Type attribute of the html input element
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeInput(
        string $inputType,
        string $key,
        string $label = null,
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
        $data =  [
            'item_type' => 'input',
            'input_type' => $inputType,
            'key' => $key,
            'label' => $label ?? $key,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * function makeTextarea
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeTextarea(
        string $key,
        string $label = null,
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
        $data =  [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.textarea',
            'key' => $key,
            'label' => $label ?? $key,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * Function makeSuggestlist
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label for the input element.
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList']. The method may return a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options_src method is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options_src method is collection. Default: 'name'.
     * @param array $options_display_keys Optional. Required for custom rendering of the list with multiple columns. Used along with the parameter 'list_component'
     * @param string $list_component Optional. Partial component used for custom rendering of the list. Used along with the parameter 'options_display_keys'
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param string|null $options_src_trigger The javascript event that triggers the dynamic re-loading of options. This is useful in cases such as dependent select lists.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param string $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeSuggestlist(
        string $key,
        string $label,
        array $options_src,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        array $options_display_keys = [],
        string $list_component = 'easyadmin::inputs.parts.sgls_regular_list',
        string $none_selected = 'Select One',
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
        $data = [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.suggestlist',
            'key' => $key,
            'label' => $label,
            'options_type' => $options_type,
            'options_id_key' => $options_id_key,
            'options_text_key' => $options_text_key,
            'options_display_keys' => $options_display_keys,
            'list_component' => $list_component,
            'none_selected' => $none_selected,
            'options_src' => $options_src,
            'fetch_url' => route(
                'easyadmin.fetch',
                ['service' => $options_src[0], 'method' => $options_src[1]]
            ),
            'properties' => $properties,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];

        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }

        return $data;
    }

    /**
     * Function makeSelect
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string $label The label for the input element.
     * @param Collection|array $options This may be a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options is collection. Default: 'name'.
     * @param array $options_display_keys Optional. Required for custom rendering of the list with multiple columns. Used along with the parameter 'list_component'
     * @param string $list_component Optional. Partial component used for custom rendering of the list. Used along with the parameter 'options_display_keys'
     * @param string $none_selected This is the text displayed as placeholder or when no selection is made. Default: 'Select One'.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param string $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array $options_src An array with the service class and its method providing the options list [ServiceClass::class, 'method']. Eg: [App\Models\Product::class, 'suggestList'].
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeSelect(
        string $key,
        string $label,
        Collection|array $options,
        string $options_type = 'collection',
        string $options_id_key = 'id',
        string $options_text_key = 'name',
        array $options_display_keys = [],
        string $list_component = 'easyadmin::inputs.parts.sgls_regular_list',
        string $none_selected = 'Select One',
        array $properties = null,
        bool $fireInputEvent = false,
        array $updateOnEvents = null,
        array $resetOnEvents = null,
        array $options_src = null,
        array $toggleOnEvents = null,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array {
        $data = [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.select',
            'key' => $key,
            'label' => $label,
            'options' => $options,
            'options_type' => $options_type,
            'options_id_key' => $options_id_key,
            'options_text_key' => $options_text_key,
            'options_display_keys' => $options_display_keys,
            'list_component' => $list_component,
            'none_selected' => $none_selected,
            'properties' => $properties,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'reset_on_events' => $resetOnEvents,
            'options_src' => $options_src,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($options_src)) {
            $data['fetch_url'] = route(
                'easyadmin.fetch',
                ['service' => $options_src[0], 'method' => $options_src[1]]
            );
        }
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }

        return $data;
    }

    /**
     * function makeFileUploader
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true'].
     * @param array|null $validations Associative array of properties and values for validation. Permitted properties: max_size, mime_type.
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param string $theme 'rounded' or 'regular' theme. Default: 'regular'
     * @param bool $allowGallery Whether to allow selection from gallery (already uploaded files).
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeFileUploader(
        string $key,
        string $label = null,
        array $properties = null,
        array $validations = [],
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        string $theme = 'regular',
        bool $allowGallery = false,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array {
        $data =  [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.fileuploader',
            'key' => $key,
            'label' => $label ?? $key,
            'validations' => $validations,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'theme' => $theme,
            'allow_gallery' => $allowGallery,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * function makeImageUploader
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true'].
     * @param array|null $validations Associative array of properties and values for validation. Permitted properties: max_size, mime_type.
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param string $theme 'rounded' or 'regular' theme. Default: 'regular'
     * @param bool $allowGallery Whether to allow selection from gallery (already uploaded files).
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeImageUploader(
        string $key,
        string $label = null,
        array $properties = null,
        array $validations = [],
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        string $theme = 'regular',
        bool $allowGallery = false,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array {
        $data =  [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.imageuploader',
            'key' => $key,
            'label' => $label ?? $key,
            'validations' => $validations,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'theme' => $theme,
            'allow_gallery' => $allowGallery,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * function makeDatePicker
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $updateOnEvents Eg: ['sourcename' => [ServiceClass::class, 'method'], ...]. An associative array. Keys: Name of source elements to be listened for input events. Values: An indexed array with the name of the service class and the method which will provide the required values to be updated. If this array is empty, the value will be reset to empty string. The 'value' property of the listened event is passed to the defined service class method as an argument. The response of the method (should be a string), will be set as the value of this element.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeDatePicker(
        string $key,
        string $label = null,
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
    ): array
    {
        $data =  [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.datepicker',
            'key' => $key,
            'label' => $label ?? $key,
            'start_year' => $startYear,
            'end_year' => $endYear,
            'date_format' => $dateFormat,
            'fire_input_event' => $fireInputEvent,
            'update_on_events' => $updateOnEvents,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * function makeCheckbox
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param bool $toggle Whether to convert the checkbox to a toggle button
     * @param array|null $displayText Array representing checked/unchecked display values Eg: ['Yes', 'No']
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeCheckbox(
        string $key,
        string $label,
        bool $toggle = false,
        array|null $displayText = null,
        array $properties = null,
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array
    {
        $data = [
            'item_type' => 'input',
            'input_type' => 'easyadmin::inputs.checkbox',
            'key' => $key,
            'label' => $label ?? $key,
            'toggle' => $toggle,
            'display_text' => $displayText,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * function makeCheckboxGroup
     *
     * @param string $key The column name in the model table or the name of the relation. It will be used as the 'name' attribute of the input element. This is the variable name that will be received among the request parameters.
     * @param string|null $label The label for the input element.
     * @param Collection|array $options This may be a collection, an associative array [101 => 'Product One', ...], or an indexed array ['Product One', 'Product Two', ...] of strings.
     * @param string $options_type The return type of the $options_src method. Allowed values: 'collection', 'key_value' ,'value_only' for return types of Collection, Associative array and indexed array respectively. Default: 'collection'
     * @param string $options_id_key Required if the return type of $options is collection. Default: 'id'.
     * @param string $options_text_key Required if the return type of $options is collection. Default: 'name'.
     * @param array $options_display_keys Optional. Required for custom rendering of the list with multiple columns. Used along with the parameter 'list_component'
     * @param string $listComponent Optional. Partial component used for custom rendering of the list. Used along with the parameter 'options_display_keys'
     * @param array|null $properties Associative array of attributes & values of the html select element. Eg: ['required' => 'true']
     * @param bool $fireInputEvent Whether to fire an even on the input event of the html input. The value of the input will be included as the 'value' property of the event detail. The 'key' (name of the html element) will be included as the 'source' proerty of event detail.
     * @param array|null $resetOnEvents An indexed array of html input element names, for whose value change this field should reset. Eg: ['title', 'another_input', ..]
     * @param array|null $toggleOnEvents Toggle the visibility of the element in response to events. Format: ['sourcename' => [['condition', 'value', show(true/false)]], ...] Eg: ['gender' => [['==', 'Male', true], ['==', 'Any', true]]]
     * @param array $formTypes In which all forms the element shall be displayed. eg: ['create', 'edit']
     * @param bool $show Whether to show the element
     * @param bool $authorised Whether the user is authorised to access this element. If false, the element will not be rendered.
     * @param string $width the width of the input element. (accepted values: 'full', '1/2', '1/3', '2/3', '1/4', '3/4')
     * @return array
     */
    public static function makeCheckboxGroup(
        string $key,
        string $label,
        Collection|array $choices,
        string $choices_type = 'collection',
        string $choices_id_key = 'id',
        string $choices_text_key = 'name',
        array $choices_display_keys = [],
        string $listComponent = 'easyadmin::inputs.parts.chbxg_regular_list',
        array $properties = null,
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ): array
    {
        $data = [
            'item_type' => 'input',
            'input_type' => 'checkbox',
            'key' => $key,
            'label' => $label ?? $key,
            'choices' => $choices,
            'choices_type' => $choices_type,
            'choices_id_key' => $choices_id_key,
            'choices_text_key' => $choices_text_key,
            'choices_display_keys' => $choices_display_keys,
            'list_component' => $listComponent,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $label
     * @param string $component
     * @param boolean $toggle
     * @param array|null|null $displayText
     * @param array|null $properties
     * @param boolean $fireInputEvent
     * @param array|null $resetOnEvents
     * @param array|null $toggleOnEvents
     * @param array|null $formTypes
     * @param boolean $show
     * @param boolean $authorised
     * @param string $width
     * @return array
     */
    public static function makeDynamicInput(
        string $key,
        string $label,
        string $component,
        bool $toggle = false,
        array|null $displayText = null,
        array $properties = null,
        bool $fireInputEvent = false,
        array $resetOnEvents = null,
        array $toggleOnEvents = null,
        array $formTypes = null,
        bool $show = true,
        bool $authorised = true,
        string $width = 'full'
    ) {
        $data = [
            'item_type' => 'input',
            'input_type' => $component,
            'key' => $key,
            'label' => $label,
            'fire_input_event' => $fireInputEvent,
            'reset_on_events' => $resetOnEvents,
            'toggle_on_events' => $toggleOnEvents,
            'show' => $show,
            'authorised' => $authorised,
            'width' => $width
        ];
        if (isset($properties)) {
            $data['properties'] = $properties;
        }
        if (isset($formTypes)) {
            $data['form_types'] = $formTypes;
        }
        return $data;
    }
}
?>
