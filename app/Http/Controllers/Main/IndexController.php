<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use FFMpeg\FFProbe;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Jobs\VideoUploadJob;

class IndexController extends Controller
{
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileUpload(Request $request)
    {
        if ($request->hasFile('video')) {

            $validator = Validator::make($request->all(), [
                'video' => 'required|file|mimetypes:video/*|max:10240',
            ], [
                'video.required' => 'Поле загрузки видео обязательно для заполнения.',
                'video.file' => 'Загруженный файл должен быть файлом.',
                'video.mimetypes' => 'Загруженный файл должен быть видео файлом.',
                'video.max' => 'Максимальный размер файла должен быть 10MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $file = $request->file('video');
            $title = $request->get('title');

            $ffprobe = FFProbe::create();
            $videoInfo = $ffprobe->format($file);
            $videoDuration = $videoInfo->get('duration');
            if ($videoDuration > 15) {
                return response()->json(['errors' => ['video' => 'Видео слишком длинное. Максимальная длительность: 15 секунд.']], 422);
            }

            $filePath = $file->store('public');
            VideoUploadJob::dispatch($filePath, $title);

            $page = $request->input('page', 1);

            $videos = Video::orderBy('created_at', 'ASC')->paginate(8, ['*'], 'page', $page)->onEachSide(1);
            $videos->setPath('/');
            $newVideos = view('main.video', compact('videos'))->render();

            return response()->json(['message' => 'Видео успешно загружено', 'videos' => $newVideos], 200);
        }
    }

    /**
     * Удаление видео файлов
     *
     * @param  Video  $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileRemove(Video $video, Request $request)
    {
        File::delete(public_path("upload/480p/$video->id.mp4"));
        File::delete(public_path("upload/1080p/$video->id.mp4"));
        File::delete(public_path("upload/preview/$video->id.jpg"));

        $video->delete();

        $page = $request->input('page', 1);

        $videos = Video::orderBy('created_at', 'ASC')->paginate(8, ['*'], 'page', $page)->onEachSide(1);
        $videos->setPath('/');
        $newVideos = view('main.video', compact('videos'))->render();

        return response()->json([
            'message' => 'Видео успешно удалено',
            'videos' => $newVideos,
            'title' => $video->title,
            'id' => $video->id,
        ], 200);
    }
}
