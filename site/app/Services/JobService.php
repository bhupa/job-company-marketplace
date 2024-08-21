<?php

namespace App\Services;

use App\Models\Jobs;
use Illuminate\Support\Facades\DB;

class JobService extends BaseService
{
    public function __construct(Jobs $job)
    {
        $this->model = $job;
    }

    // this function is used to create job
    public function create($request)
    {
        if ($request->has('job')) {
            $data = $request->job;
            unset($data['job_location']);
            if (
                $request->has('job.from_when')
                && !empty($request->job['from_when'])
            ) {
                $data['from_when'] = $this->datetoTimeString(
                    $request->job['from_when']
                );
            }
            if ($request->has('job.experience')) {
                $data['experience'] =
                    $request->job['experience'] == '0'
                        ? null
                        : $request->job['experience'];
            }
            if ($request->has('job.japanese_level')) {
                $data['japanese_level'] =
                    $request->job['japanese_level'] == '0'
                        ? null
                        : $request->job['japanese_level'];
            }
        } else {
            $data = $request->except([
                '_token',
                'job_location',
                'experience',
                'japanese_level',
            ]);

            if ($request->has('from_when') && !empty($request->from_when)) {
                $data['from_when'] = $this->datetoTimeString(
                    $request->from_when
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

            $data['status'] = $request->status ? 1 : 0;
        }
        $data['user_id'] = auth()->id();
        $data['status'] = 1;

        return $this->model->create($data);
    }

    // this function is used to update job
    public function update($request, $jobs)
    {
        $data = $request->except(['_token', 'job_location']);
        // $data['holidays'] = $request->holidays;
        if ($request->has('from_when') && !empty($request->from_when)) {
            $data['from_when'] = $this->datetoTimeString($request->from_when);
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

        return tap($jobs->update($data));
    }

    public function match()
    {
        $jobseeker = auth()->user()->jobseeker;

        return $this->model
            ->where('occupation', $jobseeker->occupation)
            ->where('japanese_level', $jobseeker->japanese_level)
            ->where('experience', $jobseeker->experience)
            ->get();
    }

    // this function is used for the job filters
    public function filter($request)
    {
        $jobs = Jobs::query();
        $jobs->when(
            $request->has('occupation') && !empty($request->occupation),
            function ($query) use ($request) {
                return $query->where('occupation', $request->occupation);
            }
        );
        $jobs->when(
            $request->has('job_location') && !empty($request->job_location),
            function ($query) use ($request) {
                $locations = getDistricts($request->job_location); // Assuming this function gets districts based on IDs
                // Use the locations relationship and the 'id' column of the District model
                if ($locations) {
                    return $query->whereHas('locations', function ($query) use (
                        $locations
                    ) {
                        $query->whereIn('districts.id', $locations);
                    });
                }
            }
        );

        $jobs->when(
            $request->has('job_type') && !empty($request->job_type),
            function ($query) use ($request) {
                return $query->where('job_type', $request->job_type);
            }
        );
        $jobs->when(
            $request->has('from_when') && !empty($request->from_when),
            function ($query) use ($request) {
                return $query->whereDate(
                    'from_when',
                    '>=',
                    $request->from_when
                );
            }
        );
        $jobs->when(
            $request->has('japanese_level') && !empty($request->japanese_level),
            function ($query) use ($request) {
                return $query->where(
                    'japanese_level',
                    $request->japanese_level
                );
            }
        );
        $jobs->when(
            $request->has('experience')
                && !empty($request->$request->experience),
            function ($query) use ($request) {
                return $query->where('experience', $request->experience);
            }
        );
        $jobs->when(
            $request->has('pay_type')
            && !empty($request->pay_type),
            function ($query) use ($request) {
                return $query->where('pay_type', $request->pay_type);
            }
        );

        $jobs->when(
            $request->has('salary_from') && $request->has('salary_to'),
            function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    if ($request->has('salary_from')) {
                        $subQuery->where('salary_from', '>=', $request->salary_from);
                    }
                    if ($request->has('salary_to')) {
                        $subQuery->where('salary_to', '<=', $request->salary_to);
                    }
                    if ($request->has('salary_from') && $request->has('salary_to')) {
                        $subQuery->orWhereBetween('salary_from', [$request->salary_from, $request->salary_to]);
                        $subQuery->orWhereBetween('salary_to', [$request->salary_from, $request->salary_to]);
                    }
                });
            }
        );
        $perPage = $request->has('per_page') ? $request->get('per_page') : '30';
        $page = $request->has('page') ? $request->get('page') : '1';
        $user = auth()->user();

        if ($user->user_type == 1) {
            $userId = $user->id;

            $leftJobs = getLeftSwipeJobs();

            if (!empty($leftJobs)) {
                $jobs->whereNotIn('id', $leftJobs);
            }
            if ($request->has('previousId')) {
                $jobs->whereNotIn(
                    'id',
                    json_decode($request->previousId, true)
                );
            }

            return $jobs
                ->whereHas('user.company')
                ->whereHas('user', function ($query) {
                    $query->where('status', '!=', 3);
                })
                ->where(function ($query) {
                    $query
                        ->whereDoesntHave('user.violation')
                        ->orWhereHas('user.violation', function (
                            $violationQuery
                        ) {
                            $violationQuery->where('status', 0);
                        });
                })
                ->whereNotExists(function ($notExistsQuery) {
                    $notExistsQuery
                        ->select(DB::raw(1))
                        ->from('matching')
                        ->whereColumn('jobs.id', 'matching.job_id')
                        ->where(
                            'matching.job_seeker_id',
                            '=',
                            auth()->user()->jobseeker->id
                        )
                        ->where(function ($query) {
                            $query
                                ->whereNotNull('matching.matched')
                                ->orWhereNotNull('matching.unmatched');
                        });
                })
                ->whereNotExists(function ($notExistsQuery) {
                    $notExistsQuery
                        ->select(DB::raw(1))
                        ->from('matching')
                        ->whereColumn('jobs.id', 'matching.job_id')
                        ->where('matching.created_by', '=', auth()->user()->id);
                })
                ->select('jobs.*')
                ->whereNotNull('user_id')
                // ->inRandomOrder()
                ->withValidCompany()
                ->paginate($perPage);
        }

        return $jobs
            ->where(function ($query) {
                $query
                    ->whereDoesntHave('user.violation')
                    ->orWhereHas('user.violation', function ($violationQuery) {
                        $violationQuery->where('status', 0);
                    });
            })
            ->inRandomOrder()
            ->WithValidCompany()
            ->paginate($perPage);
    }

    public function getLists($request)
    {
        $perPage = $request->has('per_page') ? $request->get('per_page') : '30';

        return Jobs::orderBy('created_at', 'desc')
            ->where('user_id', auth()->id())
            ->paginate($perPage);
    }
}
