<?php

namespace App\Admin\Controllers;

use App\Activity;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ActivityController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '活动';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Activity());

        $grid->column('id', __('索引'));
        $grid->column('title', __('标题'));
        $grid->column('introduce', __('简介'));
        $grid->column('max_people', __('限制人数'));
        $grid->column('open_people', __('活动发起人'));
        $grid->column('address', __('活动地址'));
        $grid->column('desc', __('详情图文'));
        $grid->column('status', __('状态'))->using([
            0 => '可预约',
            1 => '进行中',
            2 => '已结束',
        ]);
        $grid->column('time', __('开始时间'));

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
        $show = new Show(Activity::findOrFail($id));

        $show->field('id', __('索引'));
        $show->field('title', __('标题'));
        $show->field('introduce', __('简介'));
        $show->field('max_people', __('限制人数'));
        $show->field('open_people', __('发起人'));
        $show->field('address', __('地址'));
        $show->field('desc', __('详情'));
        $show->field('status', __('状态'));
        $show->field('time', __('开始时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Activity());

        $form->text('title', __('标题'))->required();
        $form->text('introduce', __('简介'))->required();
        $form->number('max_people', __('限制人数'))->required();
        $form->text('open_people', __('发起人'))->required();
        $form->text('address', __('地址'))->required();
        $form->ueditor('desc', __('详情'))->required();
        $form->select('status', __('状态'))->options([
            '0'       => '可预约',
            '1'       => '进行中',
        ]);
        $form->datetime('time', __('开始时间'))->required();

        return $form;
    }
}
