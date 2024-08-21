<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Jobs;

class JobsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Jobs';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Jobs());
        $grid->model()->whereHas('user.company');
        $grid->quickSearch(function ($model, $query) {
            $model
                ->where('job_title', 'like', "%{$query}%")
                ->orWhereHas('company', function ($subquery) use ($query) {
                    $subquery->where('company_name', 'like', "%$query%");
                });
        });
        $grid->column('id', __('Id'));
        $grid->column('job_title', __('Job title'))->limit(30);
        $grid->column('required_skills', __('Required skills'))->limit(30);
        $grid->column('Company Name')->display(function () {
            return $this->user->company->company_name;
        });
        $grid->column('occupations.name', __('Occupation'));

        $grid->column('gender')->display(function ($column) {
            $gender = [
                '1' => 'Male',
                '2' => 'Female',
                '3' => 'Other',
                '4' => 'Both',
            ];

            return $gender[$column];
        });

        $grid->column('published', __('Published'))->dateFormat('Y-m-d');

        $grid->column('status')->display(function ($column) {
            if ($column == 1) {
                return '<span class="badge rounded-pill bg-success">Open</span> ';
            }
            return '<span class="badge rounded-pill bg-danger">Closed</span> ';
        });

        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d');
        $grid->column('updated_at', __('Updated at'))->dateFormat('Y-m-d');
        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });
        $grid->disableCreateButton();
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Jobs::findOrFail($id));

        $show->field('id', __('Id'));
        $show
            ->field('user.company.company_name', __('Company Name'))
            ->as(function ($company) {
                return $company->company_name;
            });
        $show->field('job_title', __('Job title'));

        $show->field('job_location', __('location'))->as(function ($location) {
            return getLocation($location);
        });
        $show->field('salary_from', __('Salary from'));
        $show->field('salary_to', __('Salary to'));
        $show->field('published', __('Published'))->dateFormat('Y-m-d');
        $show->field('gender', 'Gender')->using([
            '2' => 'Female',
            '1' => 'Male',
            '3' => 'Others',
            '4' => 'Both',
        ]);
        $show->field('age_from', __('Age from'));
        $show->field('age_to', __('Age to'));

        $show->field('japanese_level', __('Japanese level'))->using([
            '1' => 'N1',
            '2' => 'N2',
            '3' => 'N3',
            '4' => 'N4',
            '5' => 'N5',
        ]);
        $show->field('occupations.name', __('Occupation'));
        $show->field('experience', 'Experience')->using([
            '1' => 'Less than 1 year',
            '2' => 'Less than 1 years',
            '3' => 'Less than 1 years',
            '4' => '3 years or more',
        ]);
        $show->field('job_type', 'Job Type')->using([
            '1' => 'Part time',
            '2' => 'Full time',
            '3' => 'SSW',
            '4' => 'Internship',
        ]);
        $show->field('required_skills', __('Required skills'));
        $show->field('from_when', __('From when'));
        $show
            ->field('experience_required', 'experience')
            ->as(function ($experience) {
                // $this->checkTrueFalse($experience);
            });
        $show->field('pay_raise', 'pay_raise')->as(function ($pay_raise) {
            if ($pay_raise == 1) {
                return 'Yes';
            }
            return 'No';
        });
        $show->field('training', 'training')->as(function ($training) {
            if ($training == 1) {
                return 'Yes';
            }
            return 'No';
        });
        $show->field('education', 'education')->as(function ($education) {
            if ($education == 1) {
                return 'Yes';
            }
            return 'No';
        });
        $show
            ->field('women_preferred', 'women_preferred')
            ->as(function ($women_preferred) {
                if ($women_preferred == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('men_preferred', 'men_preferred')
            ->as(function ($men_preferred) {
                if ($men_preferred == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('urgent_recruitment', 'urgent_recruitment')
            ->as(function ($urgent_recruitment) {
                if ($urgent_recruitment == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('social_insurance', 'social_insurance')
            ->as(function ($social_insurance) {
                if ($social_insurance == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('english_required', 'english_required')
            ->as(function ($english_required) {
                if ($english_required == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('accommodation', 'accommodation')
            ->as(function ($accommodation) {
                if ($accommodation == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('five_days_working', ' 5 days working')
            ->as(function ($five_days_working) {
                if ($five_days_working == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('uniform_provided', 'Uniform provided')
            ->as(function ($uniform_provided) {
                if ($uniform_provided == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show
            ->field('station_chika', 'Station chika')
            ->as(function ($station_chika) {
                if ($station_chika == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        $show->field('skill_up', 'Skill up')->as(function ($skill_up) {
            if ($skill_up == 1) {
                return 'Yes';
            }
            return 'No';
        });
        $show->field('big_company', 'Big company')->as(function ($big_company) {
            if ($big_company == 1) {
                return 'Yes';
            }
            return 'No';
        });
        // $show->field('experience_required', __('Experience required'));

        $show
            ->field('temporary_staff', 'Temporary staff')
            ->as(function ($temporary_staff) {
                if ($temporary_staff == 1) {
                    return 'Yes';
                }
                return 'No';
            });
        // $show->field('employer_status', __('Employer status'));
        // $show->field('temporary_staff', __('Temporary staff'));
        $show->field('status', __('Status'));

        $show->field('created_at', __('Created at'))->dateFormat('Y-m-d');
        $show->field('updated_at', __('Updated at'))->dateFormat('Y-m-d');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Jobs());

        $form->textarea('job_title', __('Job title'));
        $form->number('job_location', __('Job location'));
        $form->decimal('salary_from', __('Salary from'));
        $form->decimal('salary_to', __('Salary to'));
        $form->number('working_hours', __('Working hours'));
        $form->number('break_time', __('Break time'));
        $form->text('holidays', __('Holidays'));
        $form->text('vacation', __('Vacation'));
        $form->number('age_from', __('Age from'));
        $form->number('age_to', __('Age to'));
        $form->number('gender', __('Gender'));
        $form->number('experience', __('Experience'));
        $form->number('japanese_level', __('Japanese level'));
        $form->textarea('required_skills', __('Required skills'));
        $form
            ->datetime('published', __('Published'))
            ->default(date('Y-m-d H:i:s'));
        $form
            ->datetime('from_when', __('From when'))
            ->default(date('Y-m-d H:i:s'));
        $form->switch('experience_required', __('Experience required'));
        $form->switch('pay_raise', __('Pay raise'));
        $form->switch('training', __('Training'));
        $form->switch('education', __('Education'));
        $form->switch('women_preferred', __('Women preferred'));
        $form->switch('men_preferred', __('Men preferred'));
        $form->switch('urgent_recruitment', __('Urgent recruitment'));
        $form->switch('social_insurance', __('Social insurance'));
        $form->switch('english_required', __('English required'));
        $form->switch('accommodation', __('Accommodation'));
        $form->switch('five_days_working', __('Five days working'));
        $form->switch('uniform_provided', __('Uniform provided'));
        $form->switch('station_chika', __('Station chika'));
        $form->switch('skill_up', __('Skill up'));
        $form->switch('big_company', __('Big company'));
        $form->switch('employer_status', __('Employer status'));
        $form->switch('temporary_staff', __('Temporary staff'));
        $form->number('status', __('Status'));
        $form->number('user_id', __('User id'));
        $form->number('occupation', __('Occupation'));

        return $form;
    }

    public function checkTrueFalse($value)
    {
        if ($value == 1) {
            return 'Yes';
        }
        return 'No';
    }
}
