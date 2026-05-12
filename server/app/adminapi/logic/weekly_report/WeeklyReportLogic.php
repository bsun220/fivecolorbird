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

namespace app\adminapi\logic\weekly_report;


use app\adminapi\logic\system_msg\SystemMsgLogic;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\dict\DictData;
use app\common\model\weekly_report\WeeklyReport;
use app\common\logic\BaseLogic;
use think\Exception;
use think\facade\Db;


/**
 * WeeklyReport逻辑
 * Class WeeklyReportLogic
 * @package app\adminapi\logic\weekly_report
 */
class WeeklyReportLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
//            $node_name = DictData::where(['value' => $params['node']])->value('name');

            $WeeklyReport = WeeklyReport::where(['user_id'=>$params['user_id'], 'node_id'=>$params['node_id']])->findOrEmpty();
            if(!$WeeklyReport->isEmpty()){
                throw new Exception("节点已存在");
            }

            $result = [];
            if (preg_match('/(\d{4})年(\d{1,2})月第(.*?)周/', $params['node'], $matches)) {
                $result = [
                    'year' => (int)$matches[1],
                    'month' => (int)$matches[2],
                    'week' => $matches[3]
                ];
            }


            $data = [
                'file_url' => $params['file_url'],
                'node_id' => $params['node_id'],
                'user_id' => $params['user_id'],

                'date' => $result['year'] ?? null,
                'month' => $result['month'] ?? null,
                'week' => $result['week'] ?? null,
            ];

            if($params['file'] && !empty($params['file'])){
                $data['file_name'] = $params['file'][0]['name'];
            }

            if($params['node'] && !empty($params['node'])){
                $data['node'] = $params['node'];
            }

            WeeklyReport::create($data);

            SystemMsgLogic::add(['content'=>"添加周报", 'user_id'=>$params['user_id']]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {

            $WeeklyReport = WeeklyReport::where(['user_id'=>$params['user_id'], 'node_id'=>$params['node_id']])
                ->where([['id', '!=', $params['id']]])->findOrEmpty();
            if(!$WeeklyReport->isEmpty()){
                throw new Exception("节点已存在");
            }

            $result = [];
            if (preg_match('/(\d{4})年(\d{1,2})月第(.*?)周/', $params['node'], $matches)) {
                $result = [
                    'year' => (int)$matches[1],
                    'month' => (int)$matches[2],
                    'week' => $matches[3]
                ];
            }


            $data = [
                'file_url' => $params['file_url'],
                'node_id' => $params['node_id'],
                'user_id' => $params['user_id'],

                'date' => $result['year'] ?? null,
                'month' => $result['month'] ?? null,
                'week' => $result['week'] ?? null,
            ];

            if($params['file'] && !empty($params['file'])){
                $data['file_name'] = $params['file'][0]['name'];
            }

            if($params['node'] && !empty($params['node'])){
                $data['node'] = $params['node'];
            }


            WeeklyReport::where('id', $params['id'])->update($data);
            SystemMsgLogic::add(['content'=>"编辑周报", 'user_id'=>$params['user_id']]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function delete(array $params): bool
    {
        SystemMsgLogic::add(['content'=>"删除周报", 'user_id'=>$params['user_id']]);
        return WeeklyReport::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function detail($params): array
    {
        $WeeklyReport = WeeklyReport::where(['id'=>$params['id']])
            ->field(['id', 'file_name', 'file_url', 'node_id', 'node', 'user_id', 'working_hours', 'actual_hours', 'unfinished_work_hours', 'overtime_hours', 'remarks', 'status'])
            ->with(['userInfo'=>function($query){
                $query->withTrashed()->withoutField(['password', 'password_mw'])->append(['role_id', 'dept_id', 'jobs_id', 'disable_desc']);
            }])
            ->find()
            ->toArray();

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        $deptName = '';
        foreach ($WeeklyReport['userInfo']['dept_id'] as $deptId) {
            $deptName .= $deptLists[$deptId] ?? '';
            $deptName .= '/';
        }

        $jobsName = '';
        foreach ($WeeklyReport['userInfo']['jobs_id'] as $jobsId) {
            $jobsName .= $jobsLists[$jobsId] ?? '';
            $jobsName .= '/';
        }

        $WeeklyReport['userInfo']['dept_name'] = trim($deptName, '/');
        $WeeklyReport['userInfo']['jobs_name'] = trim($jobsName, '/');

        return $WeeklyReport;
    }



    /**
     * @notes 审核
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function examine(array $params): bool
    {
        Db::startTrans();
        try {
            $data = $params;
            unset($data['id']);
            unset($data['userInfo']);
            unset($data['node']);

            WeeklyReport::where('id', $params['id'])->update($data);

            $params['user_id'] = WeeklyReport::where('id', $params['id'])->value('user_id');
            SystemMsgLogic::add(['content'=>"周报被审核", 'user_id'=>$params['user_id']]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

}
