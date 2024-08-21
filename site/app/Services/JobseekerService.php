<?php

namespace App\Services;

use App\Models\Jobseeker;
use App\Traits\RegistrationTrait;

class JobseekerService extends BaseService
{
    use RegistrationTrait;

    public function __construct(Jobseeker $jobseeker)
    {
        $this->model = $jobseeker;
    }
    // this function is used to filter jobseeker

    public function filter($request)
    {
        $jobseekers = Jobseeker::query();

        $jobseekers->when(
            $request->has('job_type') && !empty($request->job_type),
            function ($query) use ($request) {
                return $query->where('job_type', $request->job_type);
            }
        );
        $jobseekers->when(
            $request->has('occupation') && !empty($request->occupation),
            function ($query) use ($request) {
                return $query->where('occupation', $request->occupation);
            }
        );
        $jobseekers->when(
            $request->has('gender') && !empty($request->gender),
            function ($query) use ($request) {
                return $query->where('gender', $request->gender);
            }
        );
        // work start date added to
        $jobseekers->when(
            $request->has('start_when') && !empty($request->start_when),
            function ($query) use ($request) {
                return $query->whereDate(
                    'start_when',
                    '>=',
                    $request->start_when
                );
            }
        );
        $jobseekers->when(
            $request->has('japanese_level') && !empty($request->japanese_level),
            function ($query) use ($request) {
                return $query->where(
                    'japanese_level',
                    $request->japanese_level
                );
            }
        );
        $jobseekers->when(
            $request->has('experience')
                && !empty($request->$request->experience),
            function ($query) use ($request) {
                return $query->where('experience', $request->experience);
            }
        );
        $jobseekers->when(
            $request->has('age_from') && $request->has('age_to'),
            function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    if ($request->has('age_from')) {
                        $subQuery->whereRaw(
                            'TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= ?',
                            [$request->age_from]
                        );
                    }
                    if ($request->has('age_to')) {
                        $subQuery->whereRaw(
                            'TIMESTAMPDIFF(YEAR, birthday, CURDATE()) <= ?',
                            [$request->age_to]
                        );
                    }
                });
            }
        );

        $perPage = $request->has('per_page') ? $request->get('per_page') : '30';
        $user = auth()->user();
        if ($user->user_type == 2) {
            $userId = $user->id;
            $leftJobseekers = getLeftSwipeJobseekers();
            if (!empty($leftJobseekers)) {
                $jobseekers->whereNotIn('id', $leftJobseekers);
            }

            if ($request->has('previousId')) {
                $jobseekers->whereNotIn(
                    'id',
                    json_decode($request->previousId, true)
                );
            }

            return $jobseekers
                ->where(function ($query) {
                    $query
                        ->whereDoesntHave('user.violation')
                        ->orWhereHas('user.violation', function (
                            $violationQuery
                        ) {
                            $violationQuery->where('status', 0);
                        });
                })
               ->whereHas('user', function ($query) {
                     $query->where('status', '!=', 3);
                })
                ->whereDoesntHave('matching', function ($matchingQuery) use (
                    $user
                ) {
                    $matchingQuery->where('created_by', $user->id);
                })
                ->whereDoesntHave('matching', function ($matchingQuery) {
                    $matchingQuery
                        ->where('company_id', auth()->user()->company->id)
                        ->where(function ($innerMatchingQuery) {
                            $innerMatchingQuery
                                ->whereNotNull('matched')
                                ->orWhereNotNull('unmatched')
                                ->orWhere('favourite_by', auth()->id());
                        });
                })
                ->whereDoesntHave('matching', function ($matchingQuery) {
                    $matchingQuery
                        ->whereColumn('jobseekers.id', 'matching.job_id')
                        ->where(
                            'matching.company_id',
                            '=',
                            auth()->user()->company->id
                        )
                        ->where(function ($query) {
                            $query
                                ->whereNotNull('matching.matched')
                                ->orWhereNotNull('matching.unmatched');
                        });
                })

                ->whereNotNull('user_id')
                ->inRandomOrder()
                ->paginate($perPage);
        }

        return $jobseekers->paginate($perPage);
    }

    public function create($request)
    {
        $data = $request->except(
            '_token',
            'image',
            'birthday',
            'profile_img',
            'experience',
            'japanese_level',
            'intro_video'
        );
        if ($request->hasFile('intro_video')) {
            $video = $this->uploadImg($request->intro_video, 'intro_video');
            auth()
                ->user()
                ->update(['intro_video' => $video]);
        }

        $data['user_id'] = auth()->id();
        $data['status'] = 1;
        $data['birthday'] = $request->birthday
            ? $this->datetoTimeString($request->birthday)
            : null;
        if ($request->has('start_when') && !empty($request->start_when)) {
            $data['start_when'] = $this->datetoTimeString($request->start_when);
        }
        if ($request->hasFile('profile_img')) {
            $data['profile_img'] = $this->uploadImg(
                $request->profile_img,
                'jobseeker/ProfileImg'
            );
        }
        if ($request->has('experience')) {
            $data['experience'] =
                $request->experience == '0' ? null : $request->experience;
        }
        if ($request->has('japanese_level')) {
            $data['japanese_level'] =
                $request->japanese_level == '0'
                    ? null
                    : $request->japanese_level;
        }

        $this->addTrialAccount(auth()->user());

        return $this->model->create($data);
    }

    public function update($request, $jobseeker)
    {
        $data = $request->except(
            '_token',
            'image',
            'birthday',
            'image_ids',
            'experience',
            'japanese_level',
            'intro_video'
        );

        if ($request->hasFile('profile_img')) {
            $data['profile_img'] = $this->uploadImg(
                $request->profile_img,
                'jobseeker/ProfileImg'
            );
            $this->deleteImage($jobseeker->profile_img);
        }
        if ($request->has('birthday')) {
            $data['birthday'] = $request->birthday
                ? $this->datetoTimeString($request->birthday)
                : null;
        }

        if ($request->has('start_when') && !empty($request->start_when)) {
            $data['start_when'] = $this->datetoTimeString($request->start_when);
        }

        if ($request->has('experience')) {
            $data['experience'] =
                $request->experience == '0' ? null : $request->experience;
        }
        if ($request->has('japanese_level')) {
            $data['japanese_level'] =
                $request->japanese_level == '0'
                    ? null
                    : $request->japanese_level;
        }

        return $jobseeker->update($data);
    }
}
