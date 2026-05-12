<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\controller;

use app\adminapi\logic\ConfigLogic;
use app\adminapi\logic\WorkbenchLogic;
use app\common\model\auth\Admin;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\notices\Notices;
use app\common\model\Performance;
use app\common\model\system_msg\SystemMsg;
use app\common\service\FileService;

/**
 * 工作台
 * Class WorkbenchCotroller
 * @package app\adminapi\controller
 */
class WorkbenchController extends BaseAdminController
{

    /**
     * @notes 工作台
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 17:01
     */
    public function index()
    {
        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        $data=[];
        $userinfo = Admin::where(['id'=>$this->adminId])->find();

        $deptName = '';
        foreach ($userinfo['dept_id'] as $deptId) {
            $deptName .= $deptLists[$deptId] ?? '';
            $deptName .= '/';
        }

        $jobsName = '';
        foreach ($userinfo['jobs_id'] as $jobsId) {
            $jobsName .= $jobsLists[$jobsId] ?? '';
            $jobsName .= '/';
        }
        $hour = date('G'); // 'G' 返回 24小时格式的小时数

        if ($hour >= 6 && $hour < 12) {
            $time_text = "早上好";
        } elseif ($hour >= 12 && $hour < 19) {
            $time_text = "下午好";
        } else {
            $time_text = "晚上好";
        }

        $userinfo['dept_name'] = trim($deptName, '/');
        $userinfo['jobs_name'] = trim($jobsName, '/');
        $data['userinfo']['username'] = $userinfo->name;
        $data['userinfo']['dept_name'] = $userinfo->dept_name;
        $data['userinfo']['jobs_name'] = $userinfo->jobs_name;
        $data['userinfo']['time_text'] = $time_text;

        //绩效奖金
//        $performance = Performance::field(['merit_pay', 'reward_amount', 'cumulative_merit_pay'])->where("statistical_month", date("Y-m"))->find();

        $performance = Performance::field(['merit_pay', 'reward_amount', 'cumulative_merit_pay'])->where('user_id', $this->adminId)->find();
        $data['performance']['merit_pay'] = $performance['merit_pay']??0;
        $data['performance']['reward_amount'] = $performance['reward_amount']??0;
        $data['performance']['cumulative_merit_pay'] = $performance['cumulative_merit_pay']??0;

        // 计算两个时间戳之间的差异（秒）
        $diff_seconds = abs(time() - strtotime($userinfo->entry_time));
        // 将秒转换为天（考虑一天有86400秒）
        $days_diff = $diff_seconds / 86400;
        $days_diff_rounded = round($days_diff); // 四舍五入到最接近的整数
        $data['performance']['days_diff_rounded'] = $days_diff_rounded;

        //系统消息
        $SystemMsg = SystemMsg::where("user_id", $this->adminId)->order("create_time", "DESC")->limit('5')->select();
        $data['system_msg'] = $SystemMsg?$SystemMsg->toArray():[];

        //最新公告
        $data['notice'] = Notices::order("create_time", "DESC")->find();
        if($data['notice'] ){
            $data['notice']['dept_name'] = $deptLists[ $data['notice']['pid'] ] ?? "所有人";
        }else{
            $data['notice'] = [];
        }



        $data['menu'] = WorkbenchLogic::menu();

        return $this->data($data);
    }


    public function user_index(){

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        $data=[];
        $userinfo = Admin::where(['id'=>$this->adminId])->find();

        $deptName = '';
        foreach ($userinfo['dept_id'] as $deptId) {
            $deptName .= $deptLists[$deptId] ?? '';
            $deptName .= '/';
        }

        $jobsName = '';
        foreach ($userinfo['jobs_id'] as $jobsId) {
            $jobsName .= $jobsLists[$jobsId] ?? '';
            $jobsName .= '/';
        }

        $sexType = ConfigLogic::getDictByType('sex')['sex'];
        $sexType = array_column($sexType, null, 'value');
        $cardType = ConfigLogic::getDictByType('card_type')['card_type'];
        $cardType = array_column($cardType, null, 'value');
        $qualificationType = ConfigLogic::getDictByType('qualification_type')['qualification_type'];
        $qualificationType = array_column($qualificationType, null, 'value');

        $data['userinfo']['avatar'] = $userinfo->avatar;
        $data['userinfo']['username'] = $userinfo->name;
        $data['userinfo']['dept_name'] = trim($deptName, '/' );
        $data['userinfo']['jobs_name'] = trim($jobsName, '/' );
        $data['userinfo']['job_no'] = $userinfo->number;
        $data['userinfo']['gender'] = $sexType[$userinfo->sex]['name']??'';
        $data['userinfo']['mobile'] = $userinfo->mobile;
        $data['userinfo']['id_type'] = $cardType[$userinfo->card]['name']??'';//
        $data['userinfo']['id_number'] = $userinfo->card_val;
        $data['userinfo']['entry_date'] = $userinfo->entry_time;
        $data['userinfo']['education'] = $qualificationType[$userinfo->qualification]['name']??'';//
        $data['userinfo']['email'] = $userinfo->email;
        $data['userinfo']['age'] = $userinfo->age;
        $data['userinfo']['address'] = $userinfo->address;
        $data['userinfo']['status'] = $userinfo->disable_desc;
        $data['userinfo']['salary'] = $userinfo->salary;


        //系统消息
        $SystemMsg = SystemMsg::where("user_id", $this->adminId)->order("create_time", "DESC")->limit('5')->select();
        $data['system_msg'] = $SystemMsg;


        $arr = array_merge([0], $userinfo['dept_id']);
        //最新公告
        $data['notice']['title'] = Notices::whereIn('pid', $arr)->order("create_time", "DESC")->value('content');


        //绩效
        $performance = Performance::field(['statistical_month', 'merit_pay', 'work_score', 'issued', 'reward_amount', 'cumulative_merit_pay'])->append(['work_score_desc'])->where('user_id', $this->adminId)->select();
        $data['performance'] = $performance;

        $data['svg'] = [
            'grzx' => FileService::getFileUrl('resource/grzx.png'),
            'jxtj' => FileService::getFileUrl('resource/jxtj.png'),
            'xxtz' => FileService::getFileUrl('resource/xxtz.png'),
            'gsgg' => FileService::getFileUrl('resource/gsgg.png'),
        ];


        return $this->data($data);
    }
}
