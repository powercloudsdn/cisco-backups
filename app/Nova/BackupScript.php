<?php

namespace App\Nova;

use App\Models\Device;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
// use Outl1ne\MultiselectField\Multiselect;

class BackupScript extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\BackupScript::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    protected $variables = [
        "username" => "Device ssh username",
        "password" => "Device ssh password",
        "enable_password" => "Device enable password",
        "ip_address" => "Device ip address",
        "tftp_ip_address" => "Server Ip Address",
        "filename" => "System generated filename for backup",
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make("Name")->rules(["required"]),
            KeyValue::make("Variables")->withMeta([
                'value' => $this->variables
            ])->readonly()->help("Click on the variables bellow to copy them."),
            $this->variableFields($request),
            Code::make("Command")->rules(["required"])->language("shell"),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    protected function variableFields($request)
    {

        $items = '';

        $vars = $this->variables;

        foreach ($vars as $name => $var) {
            $items .= '<button class="mr-1 hover:bg-gray-50 dark:hover:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-gray-500 active:text-gray-600 rounded-lg px-1 -mx-1 v-popper--has-tooltip" type="button" onclick="navigator.clipboard.writeText(\'{!! $' . $name . ' !!}\')"><pre>{!! $' . $name . ' !!}</pre></button>';
        }
        $html  = '<div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="ComputedField"><div class="md:w-3/4 md:py-3 break-all lg:break-words"><div>' . $items . '</div></div></div>';

        return
            Heading::make($html)->asHtml()->hideFromDetail()->hideFromIndex();
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
