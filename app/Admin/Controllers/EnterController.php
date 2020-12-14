<?php

namespace App\Admin\Controllers;

use App\Enter;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EnterController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '报名';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Enter());

        $grid->model()->where('is_site', '=', 0);

        $grid->column('id', __('索引'));
        $grid->column('user_id', __('员工'));
        $grid->column('activity_id', __('活动'));
        $grid->column('name', __('邀约人姓名'));
        $grid->column('sex', __('邀约人性别'));
        $grid->column('old', __('邀约人年龄'));
        $grid->column('study', __('邀约人学历'));
        $grid->column('job', __('邀约人职业'));
        $grid->column('phone', __('邀约人电话'));
        $grid->column('desc', __('邀约人简介'));
        $grid->column('invite_user', __('推荐员工'));
        $grid->column('created_at', __('报名时间'));

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
        $show = new Show(Enter::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('activity_id', __('Activity id'));
        $show->field('name', __('Name'));
        $show->field('sex', __('Sex'));
        $show->field('old', __('Old'));
        $show->field('study', __('Study'));
        $show->field('job', __('Job'));
        $show->field('phone', __('Phone'));
        $show->field('desc', __('Desc'));
        $show->field('invite_user', __('Invite user'));
        $show->field('is_site', __('Is site'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Enter());

        $form->number('user_id', __('User id'));
        $form->number('activity_id', __('Activity id'));
        $form->text('name', __('Name'));
        $form->number('sex', __('Sex'));
        $form->number('old', __('Old'));
        $form->text('study', __('Study'));
        $form->text('job', __('Job'));
        $form->mobile('phone', __('Phone'));
        $form->text('desc', __('Desc'));
        $form->number('invite_user', __('Invite user'));
        $form->number('is_site', __('Is site'));

        return $form;
    }
}
