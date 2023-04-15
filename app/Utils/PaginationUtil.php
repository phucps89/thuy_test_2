<?php

namespace App\Utils;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class PaginationUtil
{
    public static function getItemPerPage(): int
    {
        /** @var Request $request */
        $request = app('request');

        return $request->get(config('repository.pagination.params.limit'), config('repository.pagination.limit', 20));
    }

    #[ArrayShape(['current_page' => 'int', 'per_page' => 'int', 'count' => 'int', 'total' => 'int', 'total_pages' => 'float'])]
    public static function getMetaPagination(LengthAwarePaginator $data): array
    {
        $length = $data->perPage();
        $totalRecord = $data->total();

        return [
            'current_page' => $data->currentPage(),
            'per_page' => $length,
            'count' => $data->count(),
            'total' => $totalRecord,
            'total_pages' => ceil($totalRecord / $length),
        ];
    }

    #[ArrayShape(['next_cursor' => "\Illuminate\Pagination\Cursor|null", 'per_page' => 'int', 'has_more_pages' => 'bool'])]
    public static function getMetaCursorPagination(CursorPaginator $data): array
    {
        $perPage = $data->perPage();

        return [
            'next_cursor' => optional($data->nextCursor())->encode(),
            'per_page' => $perPage,
            'has_more_pages' => $data->hasMorePages(),
        ];
    }

    public static function getMetaCursorPaginationAsana(?object $data): array
    {
        return [
            'next_cursor' => optional($data)->offset,
            'has_more_pages' => !empty($data),
        ];
    }
}
