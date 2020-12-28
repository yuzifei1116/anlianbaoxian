<?php

namespace App\Admin\Controllers;

use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\ImportExcel;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '员工';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        // 设置初始排序条件
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('索引'));
        $grid->column('name', __('姓名'));
        $grid->column('card', __('工号'));
        $grid->column('money', __('待捐款金额'));
        $grid->column('abe', __('主管'));
        $grid->column('abe_card', __('主管工号'));
        $grid->column('abm', __('总监'));
        $grid->column('abm_card', __('总监工号'));
        $grid->column('created_at', __('添加时间'));

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/4, function ($filter) {
                $filter->like('abe', '主管');
                $filter->like('abm', '总监'); 
            });

        });

        // 添加到列表上-导入excel
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportExcel());
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('索引'));
        $show->field('name', __('姓名'));
        $show->field('card', __('工号'));
        $show->field('money', __('待捐款金额'));
        $show->field('created_at', __('添加时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('姓名'));
        $form->text('card', __('工号'));
        $form->text('money', __('待捐款金额'));

        return $form;
    }
}
