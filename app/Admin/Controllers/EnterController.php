<?php

namespace App\Admin\Controllers;

use App\Enter;
use App\Activity;
use App\User;
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

        // 设置初始排序条件
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('索引'));
        $grid->column('user.name', __('员工'));
        $grid->column('activity.title', __('活动'));
        $grid->column('name', __('邀约人姓名'));
        $grid->column('sex', __('邀约人性别'));
        $grid->column('old', __('邀约人年龄'));
        $grid->column('study', __('邀约人学历'));
        $grid->column('job', __('邀约人职业'));
        $grid->column('phone', __('邀约人电话'));
        $grid->column('desc', __('邀约人简介'));
        $grid->column('user.name', __('推荐员工'));
        $grid->column('sign', __('是否签到'))->using([ '0' => '否', '1' => '是']);
        $grid->column('created_at', __('报名时间'));

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/4, function ($filter) {
                $filter->like('activity.title', '活动标题');
            });

            $filter->column(1/4, function ($filter) {
                $filter->like('sign', '是否签到')->select(['0' => '否', '1' => '是']);
            });
        });

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

        $show->field('id', __('索引'));
        $show->field('user.name', __('员工'));
        $show->field('activity.title', __('活动'));
        $show->field('name', __('邀约人姓名'));
        $show->field('sex', __('邀约人性别'));
        $show->field('old', __('邀约人年龄'));
        $show->field('study', __('邀约人学历'));
        $show->field('job', __('邀约人职业'));
        $show->field('phone', __('邀约人电话'));
        $show->field('desc', __('邀约人简介'));
        $show->field('user.name', __('推荐员工'));
        $show->field('sign', __('是否签到'))->using([ '0' => '否', '1' => '是']);
        $show->field('created_at', __('报名时间'));

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
