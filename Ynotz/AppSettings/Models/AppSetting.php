<?php

namespace Modules\Ynotz\AppSettings\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ynotz\AppSettings\Database\Factories\AppSettingFactory;

class AppSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AppSettingFactory::new();
    }

    public function value(): Attribute
    {
        return Attribute::make(
            get: function ($v) {
                switch($this->value_type) {
                    case 'bigInteger';
                        $v = intval($v);
                        break;
                    case 'integer';
                        $v = intval($v);
                        break;
                    case 'double';
                        $v = floatval($v);
                        break;
                    case 'boolean';
                        $v = boolval(intval($v));
                        break;
                    case 'json';
                        $v = json_decode($v);
                        break;
                    case 'date';
                        $f = config('appSettings.date_diplay_format', 'd-m-Y');
                        $v = Carbon::createFromFormat('Y-m-d', $v)->format($f);
                    case 'datetime';
                        $f = config('appSettings.datetime_diplay_format', 'd-m-Y H:i:s');
                        $v = Carbon::createFromFormat('Y-m-d H:i:s', $v)->format($f);
                        break;
                    case 'default';
                        break;
                }
                return $v;
            },
            set: function ($v) {
                switch($this->value_type) {
                    case 'boolean';
                        $v = (is_int($v) && $v > 0)
                            || (is_numeric($v) && intval($v) > 0)
                            || $v == true
                            || $v = 'true'
                            || $v = 'yes' ? 1 : 0;
                        break;
                    case 'json';
                        $v = json_encode($v);
                        break;
                    case 'date';
                        $f = config('appSettings.date_diplay_format', 'd-m-Y');
                        $v = Carbon::createFromFormat($f, $v)->format('Y-m-d');
                    case 'datetime';
                        $f = config('appSettings.datetime_diplay_format', 'd-m-Y H:i:s');
                        $v = Carbon::createFromFormat($f, $v)->format('Y-m-d H:i:s');
                        break;
                    case 'default';
                        break;
                }
                return $v;
            }
        );
    }

    public function autoManage(): Attribute
    {
        return Attribute::make(
            set: function ($v) {
                return boolval($v);
            }
        );
    }

    public function getShowComponent()
    {
        if (!$this->auto_manage || $this->value_type == 'json') {
            return 'ynotz_appsettings::display.'.Str::snake($this->slug);
        }
        return 'ynotz_appsettings::display.'.$this->getViewPartName();
    }

    public function formComponent(): Attribute
    {
        return Attribute::make(
            get: function ($v, $attributes) {
                if (!$this->auto_manage || $this->value_type == 'json') {
                    return 'ynotz_appsettings::inputs.'.Str::snake($attributes['slug']);
                }
                return 'ynotz_appsettings::inputs.'.Str::snake(Str::lower($attributes['value_type']));
            }
        );
    }

    public function viewPermissions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // dd(json_decode($value));
                return json_decode($value);
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }

    public function editPermissions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // dd(json_decode($value));
                return json_decode($value);
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }

    public function deletePermissions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // dd(json_decode($value));
                return json_decode($value);
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }

    private function getViewPartName(): string
    {
        $partName = 'text';
        switch($this->value_type) {
            case 'boolean';
                $partName = 'bool';
                break;
            case 'date';
                $partName = 'date';
            case 'datetime';
                $partName = 'datetime';
                break;
            case 'default';
                break;
        }
        return $partName;
    }
}
