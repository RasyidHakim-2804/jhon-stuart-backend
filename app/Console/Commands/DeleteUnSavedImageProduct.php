<?php

namespace App\Console\Commands;

use App\Models\Image\ProductImage;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUnSavedImageProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-un-saved-image-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $images = ProductImage::query()
                    ->where('is_saved', '=', false)
                    ->where('created_at', '<=',now()->subMinutes(30))
                    ->get();
        
        foreach ($images as $image) {
        
            try {
                DB::beginTransaction();
                
                $imageService = new ImageService($image);
                $imageService->delete();
                $image->delete();

                DB::commit();

            } catch (\Throwable $th) {
        
                DB::rollBack();
                $imageService->rollback();
                throw $th;
            }
        }
    }
}
