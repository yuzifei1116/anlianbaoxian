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

        $grid->column('id', __('索引'));
        $grid->column('name', __('姓名'));
        $grid->column('card', __('工号'));
        $grid->column('created_at', __('添加时间'));

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

        return $form;
    }
}
