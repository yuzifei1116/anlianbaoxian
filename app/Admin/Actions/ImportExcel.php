<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Str;
use Encore\Admin\Admin;
use App\Imports\DataExcel;
use Encore\Admin\Actions\Response;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Jobs\SendMoney;

class ImportExcel extends Action
{
    protected $selector = '.import-excel';

    public function handle(Request $request)
    {
        try{
        
            $files = Excel::toArray(null, request()->file('file'));
            // 只取第一个Sheet
            if (count($files[0]) > 0) 
            {
                $rows = $files[0];
                
                $headings = [];

                if (count($rows) > 0) {
                    
                    foreach ($rows[0] as $key => $col) $headings[Str::snake($col)] = $key;

                    foreach ($rows[1] as $k => $c) $headings[Str::snake($c)] = $k;

                    foreach ($rows[2] as $l => $p) $headings[Str::snake($p)] = $l;

                }
                
                $data = [];
                
                foreach ($rows as $key => $row) 
                {
                    if ( $key > 0 && isset($row[$headings['name']]) && isset($row[$headings['card']]) && isset($row[$headings['money']]) ){

                        $data[] = [
                            'name'=>$row[$headings['name']],
                            'card'=>$row[$headings['card']],
                            'money'=>$row[$headings['money']],
                            'abe'=>$row[$headings['abe']],
                            'abe_card'=>$row[$headings['abe_card']],
                            'abm'=>$row[$headings['abm']],
                            'abm_card'=>$row[$headings['abm_card']],
                        ];

                    } 
                }
                
                foreach($data as $k=>$v){

                    if(\App\User::where(['name'=>$v['name'],'card'=>$v['card']])->first()){

                        $user = \App\User::where('name',$v['name'])->where('card',$v['card'])->first();

                        \App\User::where('name',$v['name'])->where('card',$v['card'])->update(
                            ['name'=>$v['name'],
                            'card'=>$v['card'],
                            'money'=>$v['money'] + $user->money,
                            'abe'=>$v['abe'],
                            'abe_card'=>$v['abe_card'],
                            'abm'=>$v['abm'],
                            'abm_card'=>$v['abm_card'],
                            ]
                        );
                        
                    }else \App\User::create([
                        'name'=>$v['name'],
                        'card'=>$v['card'],
                        'money'=>$money,
                        'abe'=>$v['abe'],
                        'abe_card'=>$v['abe_card'],
                        'abm'=>$v['abm'],
                        'abm_card'=>$v['abm_card'],
                    ]);

                }

                //分发队列--处理给用户发模板消息捐款--同步调度
                SendMoney::dispatchNow($data);
                
                return $this->response()->success('导入成功')->refresh();

            } else  return $this->response()->success('无数据!')->refresh();

        } catch (ValidationException $validationException) {

            return Response::withException($validationException);

        } catch (Throwable $throwable) {

            $this->response()->status = false;
            return $this->response()->swal()->error($throwable->getMessage());

        }

        return $this->response()->success('导入成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-excel">上传员工信息</a>
HTML;
    }

    // 上传表单
    public function form()
    {
        $this->file('file', '上传员工信息')->rules('required', ['required' => '文件不能为空']);
    }


    /**
     * @return string
     * 上传效果
     */
    public function handleActionPromise()
    {
        $resolve = <<<'SCRIPT'
var actionResolverss = function (data) {
            $('.modal-footer').show()
            $('.tips').remove()
            var response = data[0];
            var target   = data[1];

            if (typeof response !== 'object') {
                return $.admin.swal({type: 'error', title: 'Oops!'});
            }

            var then = function (then) {
                if (then.action == 'refresh') {
                    $.admin.reload();
                }

                if (then.action == 'download') {
                    window.open(then.value, '_blank');
                }

                if (then.action == 'redirect') {
                    $.admin.redirect(then.value);
                }
            };

            if (typeof response.html === 'string') {
                target.html(response.html);
            }

            if (typeof response.swal === 'object') {
                $.admin.swal(response.swal);
            }

            if (typeof response.toastr === 'object') {
                $.admin.toastr[response.toastr.type](response.toastr.content, '', response.toastr.options);
            }

            if (response.then) {
              then(response.then);
            }
        };

        var actionCatcherss = function (request) {
            $('.modal-footer').show()
            $('.tips').remove()

            if (request && typeof request.responseJSON === 'object') {
                $.admin.toastr.error(request.responseJSON.message, '', {positionClass:"toast-bottom-center", timeOut: 10000}).css("width","500px")
            }
        };
SCRIPT;

        Admin::script($resolve);

        return <<<'SCRIPT'
         $('.modal-footer').hide()
         let html = `<div class='tips' style='color: red;font-size: 18px;'>导入时间取决于数据量，请耐心等待结果不要关闭窗口！<img src="data:image/gif;base64,R0lGODlhEAAQAPQAAP///1VVVfr6+np6eqysrFhYWG5ubuPj48TExGNjY6Ojo5iYmOzs7Lq6utjY2ISEhI6OjgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAAFUCAgjmRpnqUwFGwhKoRgqq2YFMaRGjWA8AbZiIBbjQQ8AmmFUJEQhQGJhaKOrCksgEla+KIkYvC6SJKQOISoNSYdeIk1ayA8ExTyeR3F749CACH5BAkKAAAALAAAAAAQABAAAAVoICCKR9KMaCoaxeCoqEAkRX3AwMHWxQIIjJSAZWgUEgzBwCBAEQpMwIDwY1FHgwJCtOW2UDWYIDyqNVVkUbYr6CK+o2eUMKgWrqKhj0FrEM8jQQALPFA3MAc8CQSAMA5ZBjgqDQmHIyEAIfkECQoAAAAsAAAAABAAEAAABWAgII4j85Ao2hRIKgrEUBQJLaSHMe8zgQo6Q8sxS7RIhILhBkgumCTZsXkACBC+0cwF2GoLLoFXREDcDlkAojBICRaFLDCOQtQKjmsQSubtDFU/NXcDBHwkaw1cKQ8MiyEAIfkECQoAAAAsAAAAABAAEAAABVIgII5kaZ6AIJQCMRTFQKiDQx4GrBfGa4uCnAEhQuRgPwCBtwK+kCNFgjh6QlFYgGO7baJ2CxIioSDpwqNggWCGDVVGphly3BkOpXDrKfNm/4AhACH5BAkKAAAALAAAAAAQABAAAAVgICCOZGmeqEAMRTEQwskYbV0Yx7kYSIzQhtgoBxCKBDQCIOcoLBimRiFhSABYU5gIgW01pLUBYkRItAYAqrlhYiwKjiWAcDMWY8QjsCf4DewiBzQ2N1AmKlgvgCiMjSQhACH5BAkKAAAALAAAAAAQABAAAAVfICCOZGmeqEgUxUAIpkA0AMKyxkEiSZEIsJqhYAg+boUFSTAkiBiNHks3sg1ILAfBiS10gyqCg0UaFBCkwy3RYKiIYMAC+RAxiQgYsJdAjw5DN2gILzEEZgVcKYuMJiEAOwAAAAAAAAAAAA=="><\/div>`
         $('.modal-header').append(html)
process.then(actionResolverss).catch(actionCatcherss);
SCRIPT;
    }

}