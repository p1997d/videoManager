<?php

namespace App\Services;

use FFMpeg\FFProbe;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use App\Jobs\VideoUploadJob;
use Illuminate\Support\Facades\Validator;

class VideoHandlerService
{
    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'video' => 'required|file|mimetypes:video/*|max:10240',
        ], [
            'video.required' => 'Поле загрузки видео обязательно для заполнения.',
            'video.file' => 'Загруженный файл должен быть файлом.',
            'video.mimetypes' => 'Загруженный файл должен быть видео файлом.',
            'video.max' => 'Максимальный размер файла должен быть 10MB.',
        ]);
    }

    public function upload(Request $request)
    {
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

    public function remove(Video $video, Request $request)
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
