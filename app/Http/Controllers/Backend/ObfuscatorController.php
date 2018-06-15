<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class DashboardController.
 */
class ObfuscatorController extends Controller
{

    private $perPage = 25;

    public function __construct(){

        $this->perPage = config('log-viewer.per-page', $this->perPage);
    }
    /**
     * @return \Illuminate\View\View
     */
    public function getLogs(Request $request)
    {
        $activities = Activity::all()->reverse();



        $items = array_map(function ($item) {

            switch($item['properties']->get('level')){
                case 'alert':
                    $item['color'] = 'warning';
                    break;
                case 'error':
                    $item['color'] = 'danger';
                    break;
                case 'success':
                    $item['color'] = 'success';
                    break;
                default:
                    $item['color'] = 'info';
                    break;
            }

            return $item;
        }, $activities->toArray());

        $rows = $this->paginate($items, $request);


        return view('backend.obfuscator.logs', compact('activities', 'rows'));
    }

    /**
     * Paginate logs.
     *
     * @param  array                     $data
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $data = new Collection($data);
        $page = $request->get('page', 1);
        $path = $request->url();

        return new LengthAwarePaginator(
            $data->forPage($page, $this->perPage),
            $data->count(),
            $this->perPage,
            $page,
            compact('path')
        );
    }
}
