<?php

namespace Database\Seeders;

use App\Models\ImageFiles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = public_path('faker-image'); // Path to the image you want to upload
        $files = \File::allFiles($imagePath);
        $companies = User::where('user_type', 1)->take(10);
        $jobseekers = User::where('user_type', 2)->take(10);
        $imageName = 'uploaded_image.jpg'; // Name to be used for the uploaded image
        $combinedUsers = $companies->union($jobseekers)->get();
        foreach ($combinedUsers as $user) {
            foreach ($files as $file) {
                $name = str_replace(' ', '_', $file->getFilename());
                $filename = time().'-'.$name;
                // $filePath = $file->storeAs('jobseeker', $filename, 'public');

                if ($user->user_type == 1) {
                    $id = $user->jobseeker->id;
                    $model = 'App\Models\Jobseeker';
                    $path = 'jobseeker';
                } else {
                    $id = $user->company->id;
                    $model = 'App\Models\Company';
                    $path = 'company';
                }
                $filePath = Storage::disk('public')->putFileAs($path, $file, $filename);
                $output = ImageFiles::create([
                      'image' => $filePath,
                      'model_id' => $id,
                      'model' => $model,
                 ]);
            }
        }
    }
}
