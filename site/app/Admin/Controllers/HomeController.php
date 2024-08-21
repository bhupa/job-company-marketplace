<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Jobs;
use App\Models\Jobseeker;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\Dashboard;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->css_file(Admin::asset('open-admin/css/pages/dashboard.css'))
            ->title('Dashboard')
            ->row(function (Row $row) {
                $row->column(4, function (Column $column) {
                    // $column->append(Dashboard::environment());
                    $jobs = Jobs::count();
                    $column->append(
                        view('admin.analytic.job', compact('jobs'))
                    );
                });

                $row->column(4, function (Column $column) {
                    // $column->append(Dashboard::extensions());
                    $companies = Company::count();
                    $column->append(
                        view('admin.analytic.company', compact('companies'))
                    );
                });

                $row->column(4, function (Column $column) {
                    // $column->append(Dashboard::dependencies());
                    $jobseekers = Jobseeker::count();
                    $column->append(
                        view('admin.analytic.jobseeker', compact('jobseekers'))
                    );
                });
            });
    }
}
