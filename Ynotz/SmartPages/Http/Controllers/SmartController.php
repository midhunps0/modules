<?php

namespace Modules\Ynotz\SmartPages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Ynotz\Metatags\Helpers\MetatagHelper;

class SmartController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function buildResponse($view, $args = [])
    {
        if ($this->request->is('api/*')) {
            return response()->json($args);
        }
        // $fragment = $this->request->input('x_fr', 'fp');
        $fragment = $this->request->header('X-Fr', 'fp');

        if ($fragment != 'fp') {
            $result = ['html' => view($view, $args)->fragment($fragment)];
            if (MetatagHelper::getMetatags() != null) {
                $result['x_metatags'] = MetatagHelper::getMetatags();
            }
            if (MetatagHelper::getTitle() != null) {
                $result['x_title'] = MetatagHelper::getTitle();
            }
            MetatagHelper::clearAllMeta();
            return $result;
        }
        return view($view, $args);
    }
}
