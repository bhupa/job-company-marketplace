<?php

namespace App\Traits;

use App\Models\Jobs;

trait MatchTrait
{
    public function handleMatchedType($match)
    {
        $acptRequest = request();
        $acptRequest->merge(['type' => 'accept']);
        if (
            $match
            && ($match->favourite_by === null
                || $match->favourite_by !== $match->created_by)
        ) {
            return $this->accept($acptRequest, $match);
        } elseif (
            $match
            && $match->favourite_by !== null
            && $match->favourite_by === $match->created_by
        ) {
            $match->update(['favourite_by' => null]);
        }
    }

    public function shouldCreateMatch(
        $data,
        $type,
        $subscription,
        $userRequest,
        $match
    ) {
        return !empty($data)
            && $type == 1
            && empty($match)
            && (empty($subscription)
                || $userRequest < 10
                || !empty($subscription));
    }

    public function createMatch($data)
    {
        return is_array($data) ? $this->model->create($data) : $data;
    }

    public function handleMatchCreation($subscription, $userRequest)
    {
        $this->createFlipCount($subscription, $userRequest);
    }

    public function handleRejectedType(
        $type,
        $request,
        &$jobs,
        &$jobseekers,
        $key,
        $matchType
    ) {
        if ($type == 0) {
            if (auth()->user()->user_type === 1) {
                $jobs[] = $request->job_id[$key];
                $job = Jobs::find($request->job_id[$key]);
                $match = $this->checkCompanyAndEmployer(
                    $type,
                    auth()->user()->jobseeker->id,
                    $job->user->company->id,
                    $job->id,
                    $matchType
                );
                $match && $match->delete();
            } else {
                $jobseekers[] = $request->job_seeker_id[$key];
                $match = $this->checkCompanyAndEmployer(
                    $type,
                    $request->job_seeker_id[$key],
                    auth()->user()->company->id,
                    null,
                    $matchType
                );
                $match && $match->delete();
            }
        }
    }

    public function handleLeftSwipe($jobs, $jobseekers)
    {
        auth()->user()->user_type === 1
            && !empty($jobs)
            && setLeftSwipeJobs($jobs);
        auth()->user()->user_type === 2
            && !empty($jobseekers)
            && setLeftSwipeJobseekers($jobseekers);
    }
}
