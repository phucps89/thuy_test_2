<?php

namespace App\Http\Controllers;

use App\Services\ResponseService;
use App\Utils\PaginationUtil;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseCursorPagination(CursorPaginator $paginator): Response
    {
        $pagination = PaginationUtil::getMetaCursorPagination($paginator);
        $items = $paginator->items();

        /** @var ResponseService $responseService */
        $responseService = app(ResponseService::class);

        return $responseService->send($items, Response::HTTP_OK, null, null, [
            'pagination' => $pagination,
        ]);
    }
}
