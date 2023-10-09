<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Services\VideoHandlerService;
use App\Http\Requests\VideoUploadRequest;

class IndexController extends Controller
{
    protected $videoHandlerService;

    public function __construct(VideoHandlerService $videoHandlerService)
    {
        $this->videoHandlerService = $videoHandlerService;
    }

    /**
     * Отображение видео на главной странице.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $videos = Video::orderBy('created_at', 'ASC')->paginate(8)->onEachSide(1);

        return view('main.index', compact('videos'));
    }

    /**
     * Загрузка видео файлов
     *
     * @param  VideoUploadRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileUpload(VideoUploadRequest $request)
    {
        return $this->videoHandlerService->upload($request);
    }

    /**
     * Удаление видео файлов
     *
     * @param  Video  $video
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileRemove(Video $video, Request $request)
    {
        return $this->videoHandlerService->remove($video, $request);
    }
}
