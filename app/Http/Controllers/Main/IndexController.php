<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


class IndexController extends Controller
{
    /**
     * Отображение видео на главной странице.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $videos = Video::orderBy('created_at', 'ASC')->paginate(12)->onEachSide(1);

        return view('main.index', compact('videos'));
    }

    /**
     * Загрузка видео файлов
     *
     * @param  Request  $request
     * @return void
     */
    public function fileUpload(Request $request)
    {
        if ($request->hasFile('upload')) {

            $validator = Validator::make($request->all(), [
                'upload' => 'required|file|mimetypes:video/*|max:10240',
            ], [
                'upload.required' => 'Поле загрузки видео обязательно для заполнения.',
                'upload.file' => 'Загруженный файл должен быть файлом.',
                'upload.mimetypes' => 'Загруженный файл должен быть видео файлом.',
                'upload.max' => 'Максимальный размер файла должен быть 10MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $file = $request->file('upload');
            $fileId = Str::uuid();

            $ffmpeg = FFMpeg::create();
            $ffprobe = FFProbe::create();

            $video = $ffmpeg->open($file);
            $videoInfo = $ffprobe->format($file);
            $videoHeight = $video->getStreams()->first()->getDimensions()->getHeight();
            $videoDuration = $videoInfo->get('duration');

            if ($videoDuration > 15) {
                return response()->json(['errors' => ['upload' => 'Видео слишком длинное. Максимальная длительность: 15 секунд.']], 422);
            }

            if ($videoHeight > 1080) {
                $video
                    ->filters()
                    ->resize(new Dimension(1920, 1080))
                    ->synchronize();
            }
            $video
                ->save(new X264(), "upload/1080p/$fileId.mp4");
            $video
                ->filters()
                ->resize(new Dimension(854, 480))
                ->synchronize();
            $video
                ->frame(TimeCode::fromSeconds($videoDuration / 2))
                ->save("upload/preview/$fileId.jpg");
            $video
                ->save(new X264(), "upload/480p/$fileId.mp4");

            Video::create([
                'id' => $fileId,
                'title' => $request->get('title'),
                'duration' => $videoDuration,
                'size' => $videoInfo->get('size'),
            ]);

            return response()->json(['message' => 'Видео успешно загружено'], 200);
        }
    }

    /**
     * Удаление видео файлов
     *
     * @param  Video  $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fileRemove(Video $video)
    {
        File::delete(public_path("upload/480p/$video->id.mp4"));
        File::delete(public_path("upload/1080p/$video->id.mp4"));
        File::delete(public_path("upload/preview/$video->id.jpg"));

        $video->delete();

        return redirect()->route('main.index');
    }

}
