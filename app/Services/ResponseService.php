<?php

namespace App\Services;

use App\Utils\PaginationUtil;
use App\Utils\ValidationUtil;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ResponseService
{
    /**
     * @param mixed $data
     * @param int|null $code
     * @param string|null $messageCode
     * @param string|null $message
     * @param array $meta
     *
     * @return mixed
     */
    public function send(mixed $data, ?int $code = Response::HTTP_OK, ?string $messageCode = null, ?string $message = null, array $meta = []): mixed
    {
        $messageCodes = $messageCode ?? [];
        if (!is_null($messageCode) && !is_array($messageCode)) {
            $messageCodes = [$messageCode];
        }
        $result = [
            'messages' => [],
            'data' => null,
            'meta' => $meta == [] ? null : $meta,
        ];

        if ($data instanceof ValidationException) {
            $listMsgCode = ValidationUtil::convertToListMessageCode($data);
            foreach ($listMsgCode as $msgCode) {
                $result['messages'][] = [
                    'message_code' => $msgCode['message_code'],
                    'message' => $msgCode['message'],
                ];
            }
        } elseif ($data instanceof \Exception || $data instanceof \Throwable) {
            $result['messages'][] = [
                'message_code' => $messageCode ?? 'unknown_error',
                'message' => $data->getMessage() ?? config('messagecode.unknown_error'),
            ];
            $enable = env('APP_DEBUG');
            $enable = filter_var($enable, FILTER_VALIDATE_BOOLEAN);
            if ($enable) {
                $result['traces'] = $this->getExceptionTrace($data);
            }
        } else {
            $result['data'] = $data;
            foreach ($messageCodes as $msgCode) {
                $configMessageCode = config('messagecode.' . $msgCode, $msgCode);
                $configMessageCode = Arr::wrap($configMessageCode);
                $result['messages'][] = [
                    'message_code' => $msgCode,
                    'message' => $message ?? __('messages.' . $configMessageCode[0] ?? $configMessageCode, $configMessageCode[1] ?? []),
                ];
            }
        }

        if (empty($result['messages'])) {
            unset($result['messages']);
        }


        if (empty($result['meta'])) {
            unset($result['meta']);
        }

        return response($result, $code);
    }

    public function pagination(LengthAwarePaginator $data)
    {
        $meta = PaginationUtil::getMetaPagination($data);

        return $this->send($data->items(), Response::HTTP_OK, null, null, [
            'pagination' => $meta,
        ]);
    }

    /**
     * @param \Exception|\Throwable $exception
     *
     * @return mixed
     */
    private function getExceptionTrace($exception)
    {
        $traceStr = $exception->getTraceAsString();
        $arr = preg_split('/#\d+\s+/', trim($traceStr));
        unset($arr[0]);

        return array_chunk($arr, 10)[0];
    }

    /**
     * @param string $path
     */
    public function display(string $path)
    {
        header('Content-type: ' . $this->mimeType($path));
        readfile($path);
    }

    private function mimeType($path)
    {
        preg_match("|\.([a-z0-9]{2,4})$|i", $path, $fileSuffix);
        if (empty($fileSuffix)) {
            return mime_content_type($path);
        }

        switch (strtolower($fileSuffix[1])) {
            case 'js':
                return 'application/x-javascript';
            case 'json':
                return 'application/json';
            case 'jpg':
            case 'jpeg':
            case 'jpe':
                return 'image/jpg';
            case 'png':
            case 'gif':
            case 'bmp':
            case 'tiff':
                return 'image/' . strtolower($fileSuffix[1]);
            case 'css':
                return 'text/css';
            case 'xml':
                return 'application/xml';
            case 'doc':
            case 'docx':
                return 'application/msword';
            case 'xls':
            case 'xlt':
            case 'xlm':
            case 'xld':
            case 'xla':
            case 'xlc':
            case 'xlw':
            case 'xll':
                return 'application/vnd.ms-excel';
            case 'ppt':
            case 'pps':
                return 'application/vnd.ms-powerpoint';
            case 'rtf':
                return 'application/rtf';
            case 'pdf':
                return 'application/pdf';
            case 'html':
            case 'htm':
            case 'php':
                return 'text/html';
            case 'txt':
                return 'text/plain';
            case 'mpeg':
            case 'mpg':
            case 'mpe':
                return 'video/mpeg';
            case 'mp3':
                return 'audio/mpeg3';
            case 'wav':
                return 'audio/wav';
            case 'aiff':
            case 'aif':
                return 'audio/aiff';
            case 'avi':
                return 'video/msvideo';
            case 'wmv':
                return 'video/x-ms-wmv';
            case 'mov':
                return 'video/quicktime';
            case 'zip':
                return 'application/zip';
            case 'tar':
                return 'application/x-tar';
            case 'swf':
                return 'application/x-shockwave-flash';
            default:
                if (function_exists('mime_content_type')) {
                    $fileSuffix = mime_content_type($path);
                }

                return 'unknown/' . trim($fileSuffix[0], '.');
        }
    }
}
