<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use App\Models\Video;

class VideoUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath, $title;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $title)
    {
        $this->filePath = $filePath;
        $this->title = $title;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileId = Str::uuid();

        $ffmpeg = FFMpeg::create();
        $ffprobe = FFProbe::create();

        $video = $ffmpeg->open(storage_path('app/' . $this->filePath));
        $videoInfo = $ffprobe->format(storage_path('app/' . $this->filePath));
        $videoHeight = $video->getStreams()->first()->getDimensions()->getHeight();
        $videoDuration = $videoInfo->get('duration');

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
            'title' => $this->title,
            'duration' => $videoDuration,
            'size' => $videoInfo->get('size'),
        ]);

        Storage::delete($this->filePath);
    }
}
